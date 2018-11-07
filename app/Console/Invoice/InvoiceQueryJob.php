<?php
/**
 * Created by PhpStorm.
 * User: cecyu
 * Date: 2018/6/20
 * Time: 下午 03:03
 */

namespace App\Console\Invoice;

// 核心
use Illuminate\Console\Command;

use App\Models\idelivery\Invoice;
use App\Models\idelivery\Invoice_detail;

/**
 * 電子發票 開立發票 串接新電
 * * php artisan Invoice:InvoiceQueryJob
 */
class InvoiceQueryJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:InvoiceQueryJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[電子發票]查詢發票上傳財政部狀態';

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

        //擷取50筆發票資料上傳
        $invoice = Invoice::where("upload", 0)
            ->groupBy("invoice_date")
            ->select("invoice_date")
            ->get();

        $invoice_list = Invoice::where("upload", 0)->get();
        if (!empty($invoice)) {

            foreach ($invoice as $val)
            {
                $invoice_date = date("Y/m/d", strtotime($val->invoice_date));

                $api_data = array(
                    "ID" => env("INVOICE_THIRD_ID"),
                    "Verify" => env("INVOICE_THIRD_VERIFY"),
                    "InvoiceDate" => $invoice_date,
                );

                //打第三方api
                $api_url = env("INVOICE_MAIN_QUERY_URL");
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

                if (!empty($result)) {
                    $result = json_decode($result, true);
                    //解析第三方回傳訊息
                    if (!empty($result["Data"])) {
                        foreach ($result["Data"] as $val) {
                            if (!empty($val["UploadType"])) {
                                $order = $invoice_list->where("order_id", $val["OrderNumber"])->first();
                                if ($val["UploadType"] == 2)
                                {
                                    $order->upload = 1;
                                    $order->save();
                                }
                            }
                        }
                    }
                }
            }
            $this->info('上傳完成!');
        }

        unset($order);
        unset($result);
        unset($invoice_list);
        unset($api_data);
        unset($invoice);

        $this->info('END');
    }

}