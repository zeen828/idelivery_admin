<?php
/**
 * Created by PhpStorm.
 * User: cecyu
 * Date: 2018/6/13
 * Time: 上午 10:12
 */

namespace App\Console\Invoice;

// 核心
use Illuminate\Console\Command;

use App\Models\idelivery\Invoice;
use App\Models\idelivery\Invoice_detail;
use App\Models\idelivery\Store;

/**
 * 電子發票 開立發票 串接新電
 * * php artisan Invoice:EZinvoiceGenJob
 */
class EZinvoiceGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:EZinvoiceGenJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[電子發票]上傳第三方新電科技';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('START');

        //店家使用新電科技作為電子發票上傳
        $stores = Store::where("invoice_third_id", 1)
            ->whereNotNull("invoice_third_account")
            ->whereNotNull("invoice_third_password")
            ->where("status", 1)
            ->select("id", "invoice_third_account", "invoice_third_password")
            ->get();

        if ($stores->isEmpty())
        {
            $this->info('END: 查無使用新電店家');
            exit();
        }

        $store_account = array();
        $store_id_arr = array();
        foreach ($stores as $val)
        {
            $store_id_arr[] = $val->id;
            $store_account[$val->id]['account'] = $val->invoice_third_account;
            $store_account[$val->id]['password'] = $val->invoice_third_password;
        }

        $this->info("新電店家" . json_encode($store_id_arr));

        $store = Invoice::select("store_id")
            ->where("upload_third", 0)
            ->whereIn("store_id", $store_id_arr)
            ->orderBy("created_at", "ASC")
            ->first();    //collection

        if (empty($store))
        {
            $this->info('END: 查無需上傳資料-1');
            exit();
        }
        $this->info("準備上傳新電店家 store_id:" . $store->store_id);

        //擷取50筆發票資料上傳
        $invoice_src = Invoice::where("upload_third", 0)
            ->where("store_id", $store->store_id)
            ->limit(50);

        $invoice = $invoice_src->get();

        if ($invoice->isEmpty())
        {
            $this->info('END: 查無需上傳資料-2');
            exit();
        }

        $invoice_id = $invoice->pluck("id")->toArray();
        //取得欲上傳之發票明細資料
        $invoice_detail = Invoice_detail::whereIn("invoice_id", $invoice_id)->get();

        //整理資料
        $list = array();
        $digits = 4;
        $store_id = 0;
        $first = true;
        foreach ($invoice as $value)
        {
            if ($store_id != $value->store_id)
            {
                $first = false;
                $store_id = $value->store_id;
            }

            if ($first == false)
            {
                //整理發票明細資料
                $detail_list = $invoice_detail->where("invoice_id", $value->id);
                if (!empty($detail_list))
                {
                    $detail = array();
                    foreach ($detail_list as $item)
                    {
                        $item_list = array(
                            "ProductName" => $item->description,
                            "Quantity" => $item->quantity,
                            "UnitPrice" => $item->unit_price,
                            "Amount" => $item->amount,
                        );

                        $detail[] = $item_list;
                    }
                }

                //產生電子發票隨機碼(限新電)
                $random_num = str_pad(rand(0, pow(10, $digits)-1), $digits, '0', STR_PAD_LEFT);

                //整理發票資料
                $main = array(
                    "OrderNumber" => strval($value->order_id),
                    "InvoiceDate" => date("Y/m/d H:i:s", strtotime($value->invoice_date." ".$value->invoice_time)),
                    "Invoice" => $value->invoice_no,
                    "RandomNumber" => $random_num,
                    "BIdentifier" => ($value->buyer_id = "0000000000") ? null : $value->buyer_id,
                    "BName" => $value->buyer_name,
                    "CarrierId" => empty($value->carrier_id) ? null : $value->carrier_id,
                    "PrintMark" => empty($value->print_mark) ? "N" : $value->print_mark,
                    "DonateNo" => $value->donate_mark,
                    "RName" => $value->buyer_name,
                    "RAddress" => $value->buyer_address,
                    "REmail" => $value->buyer_email,
                    "RMobile" => $value->buyer_telephone_no,
                    "SalesAMT" => $value->sales_amount,
                    "FreeTaxAMT" => $value->free_tax_amount,
                    "ZeroTaxAMT" => $value->zero_tax_amount,
                    "TaxType" => $value->tax_type,
                    "TaxRate" => $value->tax_rate,
                    "TaxAMT" => $value->tax_amount,
                    "OrderTotalAMT" => $value->total_amount,
                    "OrderInfo" => $value->main_remark,
                    "Item" => $detail,
                );

                $list[] = $main;
            }

        }

        $api_data = array(
            "ID" => $store_account[$store->store_id]['account'],
            "Verify" => $store_account[$store->store_id]['password'],
            "Data" => $list,
        );

        //打第三方api
        $api_url = env("INVOICE_MAIN_URL");
        $data = json_encode($api_data);
        //$header = array("Content-Type: application/json", "Authorization:" . base64_encode(hash("sha1", OLD_APP_KEY)));

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_URL, $api_url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        //curl_setopt($curl, CURLOPT_TIMEOUT, 1);
        //curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        $result = curl_exec($curl);
        curl_close($curl);

        //寫入上傳第三方時間及狀態
        $invoice_src->update(["upload_third" => 1,
            "upload_third_datetime" => date("Y-m-d H:i:s")]);

        if (!empty($result))
        {
            $result = json_decode($result,true);
            //解析第三方回傳訊息
            if (!empty($result["Detail"]))
            {
                foreach ($result["Detail"] as $val)
                {
                    if (!empty($val["OrderStatus"]))
                    {
                        $order = $invoice->where("order_id", $val["OrderNumber"])->first();
                        if (!empty($val["DetailErrorMessage"]))
                        {
                            $order->message_third = $val["DetailErrorMessage"];
                        }
                        else
                        {
                            $order->message_third = $val["OrderStatus"];
                        }
                        $order->save();
                    }
                }
            }
        }

        $this->info('上傳完成!');

        unset($order);
        unset($result);
        unset($invoice_src);
        unset($api_data);
        unset($list);
        unset($main);
        unset($detail);
        unset($detail_list);
        unset($invoice);
        unset($invoice_detail);

        $this->info('END');
    }

}