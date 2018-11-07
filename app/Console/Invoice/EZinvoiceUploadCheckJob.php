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

use App\Models\idelivery\Store;
use App\Models\idelivery\Invoice;
use App\Models\idelivery\Invoice_detail;
use App\Models\idelivery\Order;
use App\Models\idelivery\Invoice_log;
use App\Models\system_member\Sms_log;

/**
 * 電子發票 開立發票 串接新電
 * * php artisan Invoice:EZinvoiceUploadCheckJob --store=10 --start_date=20180827
 */
class EZinvoiceUploadCheckJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:EZinvoiceUploadCheckJob {--store= : 店家ID} {--start_date= : 查詢日EX=20180101}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[電子發票]上傳第三方新電科技發票上傳數量檢核';

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

        //判斷店家編號合法性
        $store_arr = array();
        $store = $this->option('store');
        if (!empty($store) && is_numeric($store))
        {
            $store_arr = Store::where('id', $store)->pluck('id')->toArray();
        }else{
            $store_arr = Store::where('status', 1)
                ->where('invoice_third_id', 1)
                ->whereNotNull('invoice_third_account')
                ->whereNotNull('invoice_third_password')
                ->pluck('id')->toArray();
        }

        //檢查品牌資料是否有存在
        if (empty($store_arr) || count($store_arr) <= 0)
        {
            $this->info('查無店家資料!');
            exit();
        }
        $this->info('store:' . json_encode($store_arr));

        //起始日期合法性
        $start_date = $this->option('start_date');
        if (!empty($start_date))
        {
            $start_date = date("Y-m-d", strtotime($start_date));
        }else{
            $start_date = date("Y-m-d", strtotime(date("Y-m-d")." -1 day"));
        }
        $this->info('start date:' . $start_date);

        $store_info = array();

        //第三方平台限新電科技
        $store_data = Store::whereIn("id", $store_arr)
            ->where("invoice_third_id", 1)->get();

        if (empty($store_data))
        {
            $this->info('Store data not found!');
            exit();
        }

        //擷取店家發票上傳帳密
        foreach ($store_data as $val)
        {
            $store_info[$val->id]['account'] = $val->invoice_third_account;
            $store_info[$val->id]['password'] = $val->invoice_third_password;
        }

        //所有要執行的店家
        foreach($store_arr as $store_id)
        {
            //擷取當日店家訂單資訊
            $orders_src = Order::where("store_id", $store_id)
                ->whereNotNull("create_time")//開帳日期不為空
                ->whereNotIn("status", ["1", "7"])//不為未接單及取消接單
                //->where("confirm_status", "!=", "3")//不為取消接單
                ->whereDate("create_time", "=", $start_date);

            $orders_cnt = 0;
            //計算訂單筆數
            if (count($orders_src) > 0)
            {
                $orders_cnt = $orders_src->count();
            }

            //擷取當日店家開立發票資訊
            $invoices_src = Invoice::where("store_id", $store_id)
                ->where('invoice_date', date("Ymd", strtotime($start_date)));

            $tmp_invoices_src = clone $invoices_src;
            $invoices_cnt = 0;
            $invoice_upload_cnt = 0;
            $invoice_upload_fail_cnt = 0;
            //計算發票張數
            if (count($invoices_src) > 0)
            {
                //發票張數
                $invoices_cnt = $invoices_src->count();

                //已上傳成功發票數
                $invoice_upload_cnt = $invoices_src->where("upload_third", 1)
                    ->where("message_third", "ok")->count();

                //已上傳第三方未回傳訊息數
                $invoice_upload_fail_cnt = $tmp_invoices_src->where("upload_third", 1)
                    ->whereNull("message_third")->count();

                $invoice_fail = $tmp_invoices_src->where("upload_third", 1)
                    ->whereNull("message_third")->get();
            }

            //準備新電科技5.查詢發票api資料
            $api_data = array(
                "ID" => $store_info[$store_id]["account"],
                "Verify" => $store_info[$store_id]['password'],
                "InvoiceDate" => date("Y/m/d", strtotime($start_date)),
            );

            //打 api查詢新電科技現有發票資訊
            $result = $this->invoice_api(env("INVOICE_MAIN_QUERY_URL"), $api_data);

            $callback = array();

            //解析第三方平台回傳結果
            if (!empty($result))
            {
                $result = json_decode($result, true);
                if (!empty($result["Status"]) && $result["Status"] == "ok")
                {
                    if (!empty($result["Data"]))
                    {
                        foreach ($result["Data"] as $key => $val)
                        {
                            $callback[$key]["order_id"] = $val["OrderNumber"];
                            $callback[$key]["invoice_no"] = $val["Invoice"];
                        }
                    }
                }
            }

            //第三方平台現有接收到之發票數
            $callback_upload_cnt = count($callback);

            //設定告警狀態
            $alarm = false;

            //若有上傳錯誤
            if ($invoice_upload_fail_cnt > 0 && !empty($invoice_fail))
            {
                //整理失敗重新上傳之資料
                $data = $this->invoice_data_setting($invoice_fail);

                $api_data = array(
                    "ID"        => $store_info[$store_id]["account"],
                    "Verify"    => $store_info[$store_id]['password'],
                    "Data"      => $data,
                );

                //打 api重新上傳開立之發票
                $result = $this->invoice_api(env("INVOICE_MAIN_URL"), $api_data);
                if (!empty($result))
                {
                    $result = json_decode($result, true);
                    if (!empty($result["Status"]) && $result["Status"] == "ok")
                    {
                        if (!empty($result["Detail"]))
                        {
                            foreach ($result["Detail"] as $detail)
                            {
                                $invoice = Invoice::where("store_id", $store_id)
                                    ->where('invoice_date', date("Ymd", strtotime($start_date)))
                                    ->where('order_id', (int) $detail["OrderNumber"])
                                    ->first();

                                if (!empty($invoice))
                                {
                                    if ($detail["OrderStatus"] == "ok" && empty($detail["DetailErrorMessage"]))
                                    {
                                        $invoice->message_third = $detail["OrderStatus"];
                                    }
                                    else
                                    {
                                        $alarm = true;
                                        $invoice->message_third = $detail["DetailErrorMessage"];
                                    }

                                    $invoice->upload_third = 1;
                                    $invoice->upload_third_datetime = date("Y-m-d H:i:s");
                                    $invoice->save();

                                }


//                                //上傳仍有錯誤即變更告警狀態
//                                if ($detail["OrderStatus"] == "error" || !empty($detail["DetailErrorMessage"]))
//                                {
//                                    $alarm = true;
//                                }
//                                else
//                                {
//                                    //上傳正確即修訂發票上傳欄位
//                                    $invoice = Invoice::where("store_id", $store_id)
//                                        ->where('invoice_date', date("Ymd", strtotime($start_date)))
//                                        ->where('order_id', (int) $detail["OrderNumber"])
//                                        ->first();
//
//                                    if (!empty($invoice))
//                                    {
//                                        $invoice->upload_third = 1;
//                                        $invoice->message_third = $detail["OrderStatus"];
//                                        $invoice->upload_third_datetime = date("Y-m-d H:i:s");
//                                        $invoice->save();
//                                    }
//                                }

                            }

                        }


                        //重新上傳第三方未回傳訊息數
                        $re_upload_fail_cnt = Invoice::where("store_id", $store_id)
                            ->where('invoice_date', date("Ymd", strtotime($start_date)))
                            ->where("upload_third", 1)
                            ->whereNull("message_third")
                            ->count();

                        //更新上傳成功數
                        $invoice_upload_cnt = Invoice::where("store_id", $store_id)
                            ->where('invoice_date', date("Ymd", strtotime($start_date)))
                            ->where("upload_third", 1)
                            ->where("message_third", "ok(補發)")
                            ->count();

                        //更新第三方已收發票數
                        $callback_upload_cnt = $callback_upload_cnt + (int) $result["TotalCount"];

                        //回傳上傳成功數與重傳數不符或重傳後仍有錯誤數者, 啟動告警
                        if ($result["TotalCount"] != $invoice_upload_fail_cnt
                            || $re_upload_fail_cnt != 0)
                        {
                            $alarm = true;
                        }
                        //更新上傳錯誤數
                        $invoice_upload_fail_cnt = $re_upload_fail_cnt;
                    }

                    else
                    {
                        $alarm = true;
                    }
                }
            }

            Invoice_log::updateOrCreate(['third_type_id' => 1, 'store_id' => $store_id,
                'exec_date' => $start_date], ['orders_counts' => $orders_cnt,
                'invoice_counts' => $invoices_cnt, 'upload_counts' => $invoice_upload_cnt,
                'callback_null_counts' => $invoice_upload_fail_cnt,
                'callback_counts' => $callback_upload_cnt, 'status' => 1]);

            //若告警狀態為真, 則發出簡訊給Luke, Will
            if ($alarm)
            {
                $sms_list = array();
                //發票簡訊告警對象
                $phones = config("damaiapp.SMS_ALARM");
                if (!empty($phones))
                {
                    //準備簡訊資料
                    foreach ($phones as $phone)
                    {
                        $obj = new \StdClass();
                        $obj->service_id    = config("damaiapp.SERVICE_ID");
                        $obj->company_id    = $store_data->find($store_id)->company_id;
                        $obj->store_id      = $store_id;
                        $obj->dstaddr       = $phone;

                        $sms_list[] = $obj;
                    }
                }
                //發送簡訊
                $this->sms_send($sms_list);
            }
        }

        unset($sms_list);
        unset($phones);
        unset($invoice_log);
        unset($callback);
        unset($result);
        unset($api_data);
        unset($invoices_src);
        unset($orders_src);
        unset($store_arr);
        unset($store_info);
        unset($store_data);

        $this->info('END');
    }

    //上傳發票資料準備
    private function invoice_data_setting($invoice_fail)
    {
        $invoice_id_arr = $invoice_fail->pluck('id')->toArray();

        //取得欲上傳之發票明細資料
        $invoice_detail = Invoice_detail::whereIn("invoice_id", $invoice_id_arr)->get();

        //整理資料
        $list = array();
        $digits = 4;
        $store_id = 0;
        $first = true;
        foreach ($invoice_fail as $value)
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

        return $list;
    }

    //執行第三方發票查詢 api
    private function invoice_api($api_url, $api_data)
    {
        //打第三方api
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

        return $result;
    }

    //發送簡訊
    private function sms_send($sms_list)
    {
        if (!empty($sms_list))
        {
            foreach ($sms_list as $val)
            {
                $sms = $this->sms_format();
                $sms["service_id"]    = $val->service_id;
                $sms["company_id"]    = $val->company_id;
                $sms["store_id"]      = $val->store_id;
                $sms["dstaddr"]       = $val->dstaddr;

                Sms_log::create($sms);//寫入後, 排程會自動執行
            }
        }
    }

    //簡訊格式
    private function sms_format()
    {
        $format_array = array(
            "service_id"    => 2,
            "company_id"    => 0,
            "store_id"      => 0,
            "status"        => 0,
            "priority"      => 10,
            "dstaddr"       => null,
            "smbody"        => "電子發票上傳錯誤, 請儘速處理!",
        );
        return $format_array;
    }
}