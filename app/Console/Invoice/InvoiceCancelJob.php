<?php
/**
 * Created by PhpStorm.
 * User: cecyu
 * Date: 2018/6/14
 * Time: 上午 09:17
 */
namespace App\Console\Invoice;

// 核心
use Illuminate\Console\Command;

use App\Models\idelivery\Invoice_cancel;
use App\Models\idelivery\Store;

/**
 * 電子發票 作廢發票 串接新電
 * * php artisan Invoice:EZinvoiceCancelJob
 */
class InvoiceCancelJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Invoice:EZinvoiceCancelJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '[電子發票]作廢電子發票串接第三方平台';

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
        $invoice = Invoice_cancel::where("upload_third", 0)->limit(50)->get();    //collection
        if (!empty($invoice)) {
            //整理資料
            //$list = array();
            foreach ($invoice as $value) {
                $store = Store::find($value->store_id);
                if (!empty($store) && !empty($store->invoice_third_account)
                    && !empty($store->invoice_third_password))
                {
                    //整理上傳資料
                    $api_data = array(
                        "ID" => $store->invoice_third_account,
                        "Verify" => $store->invoice_third_password,
                        "InvoiceNum" => $value->invoice_no,
                    );

                    //打第三方api-限單筆傳送作廢
                    $api_url = env("INVOICE_MAIN_CANCEL_URL");
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
                    $value->upload_third = 1;
                    $value->upload_third_datetime = date("Y-m-d H:i:s");
                    //$value->save();

                    //解析第三方回傳訊息
                    $value->message_third = $result;
                    $value->save();

                }
            }
            $this->info('上傳完成!');
        }
        $this->info('END');
    }

}