<?php
/**
 * Created by PhpStorm.
 * User: cecyu
 * Date: 2018/8/1
 * Time: 上午 10:22
 */

namespace App\Console\Reports;

// 核心
use Illuminate\Console\Command;

use App\Models\system_damaiapp\Company as DamaiappCompany;
use App\Models\idelivery\Order;
use App\Models\idelivery\Report_campaign;
use App\Models\idelivery\Campaign_setting;
use App\Models\idelivery\Company;
use App\Models\idelivery\Store;

use DB;

/**
 * 愛外送 報表統計
 * php artisan Reports:ReportCampaignGenJob --company=3 --start_datetime=20180808010000
 */
class ReportCampaignGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reports:ReportCampaignGenJob {--company= : 品牌ID} {--start_datetime= : 查詢日EX=20180101010000}';
    //20180101010000 = 2018-01-01 01:00:00 ~ 2018-01-01 01:59:59

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '活動-優惠報表統計';

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

        //判斷品牌編號合法性
        $company_arr = array();
        $company = $this->option('company');
        if (!empty($company) && is_numeric($company))
        {
            $company_arr = Company::where('id', $company)->pluck('id')->toArray();
        }else{
            $company_arr = DamaiappCompany::where('sw_report', 1)->where('status', 1)->pluck('id')->toArray();
        }

        //檢查品牌資料是否有存在
        if (empty($company_arr) || count($company_arr) <= 0)
        {
            $this->info('查無品牌資料!');
            exit();
        }
        $this->info('company:' . json_encode($company_arr));

        //起始日期合法性
        $start_datetime = $this->option('start_datetime');
        if (!empty($start_datetime))
        {
            $start_datetime = date("Y-m-d H:i:s", strtotime($start_datetime));
        }else{
            $start_datetime = date("Y-m-d H:i:s", strtotime(date("Y-m-d H:i:s")." -1 hour"));
        }
        $this->info('start datetime:' . $start_datetime);

        //所有要執行的品牌
        foreach($company_arr as $company_id) {

            //取得起迄時段
            $start_hour = date("Y-m-d H", strtotime($start_datetime));
            $end_hour = $start_hour;

            $start_at = date("Y-m-d H:i:s", strtotime($start_hour.":00:00"));
            $end_at = date("Y-m-d H:i:s", strtotime($end_hour.":59:59"));

            /******************************************************************************/
            //訂單
            /******************************************************************************/
            //取得符合條件之訂單資料
            $orders_src = Order::where("company_id", '=', $company_id)
                ->whereNotNull("create_time")
                ->whereNotIn("status", ["1", "7"])//不為未接單及取消接單
                //->where("confirm_status", "!=", "3")//不為取消接單
                ->whereBetween("create_time", [$start_at, $end_at]);//符合起迄時段

            //取出符合條件之訂單編號
            $order_id_arr = $orders_src->pluck("id")->toArray();

            //取得品牌所屬店家資料
            $store_src = Store::where("company_id", $company_id);
            $store = $store_src->get();

            /******************************************************************************/
            //折扣類型
            /******************************************************************************/
            $campaigns = Order::leftJoin("campaign_log", "campaign_log.order_id", "order.id")
                ->leftJoin("coupon_log", "coupon_log.order_id", "order.id")
                ->whereIn("order.id", $order_id_arr)
                ->where("order.marketing_type", "!=", 0)
                ->whereNotNull("order.marketing_sn")
                ->select("order.company_id", "order.store_id", DB::raw("concat(date(order.create_time), 
                    ' ', hour(order.create_time)) as report_at"),
                    "order.marketing_type", "campaign_log.setting_id as campaign_setting_id",
                    "coupon_log.setting_id as coupon_setting_id",
                    DB::raw("SUM(campaign_log.total_price) as campaign_price, SUM(campaign_log.check_out_price) as campaign_checkout,
                SUM(campaign_log.deduct_price) as campaign_deduct, 
                SUM(coupon_log.total_price) as coupon_price, SUM(coupon_log.check_out_price) as coupon_checkout,
                SUM(coupon_log.deduct_price) as coupon_deduct"))
                ->groupBy("order.company_id", "order.store_id", "report_at", "order.marketing_type",
                    "campaign_setting_id", "coupon_setting_id")
                ->orderBy("order.company_id", "order.store_id")
                ->get();

            //取得品牌活動/優惠設定資料
            $campaign_settings = Campaign_setting::where("company_id", $company_id)
                ->where("status", 1)
                ->whereBetween('created_at', [$start_at, $end_at]);

            $setting_id_arr = array();

            if (!$campaigns->isEmpty()) {
                //新增折扣類型排程區間資料
                foreach ($campaigns as $campaign) {
                    $setting = null;
                    $total_price = 0;
                    $check_out_price = 0;
                    $deduct_price = 0;

                    if ($campaign->marketing_type == 1) {
                        $setting = Campaign_setting::find($campaign->campaign_setting_id);
                        $total_price = empty($campaign->campaign_price) ? 0 : $campaign->campaign_price;
                        $check_out_price = empty($campaign->campaign_checkout) ? 0 : $campaign->campaign_checkout;
                        $deduct_price = empty($campaign->campaign_deduct) ? 0 : $campaign->campaign_deduct;
                    } else {
                        $setting = Campaign_setting::find($campaign->coupon_setting_id);
                        $total_price = empty($campaign->coupon_price) ? 0 : $campaign->coupon_price;
                        $check_out_price = empty($campaign->coupon_checkout) ? 0 : $campaign->coupon_checkout;
                        $deduct_price = empty($campaign->coupon_deduct) ? 0 : $campaign->coupon_deduct;
                    }

                    $input = $this->campaign_format();
                    $input["company_id"] = $campaign->company_id;
                    $input["store_id"] = $campaign->store_id;
                    $input["years"] = date("Y", strtotime($start_datetime));
                    $input["months"] = date("m", strtotime($start_datetime));
                    $input["days"] = date("d", strtotime($start_datetime));
                    $input["hours"] = date("H", strtotime($start_datetime));
                    $input["start_hour"] = $start_at;
                    $input["end_hour"] = $end_at;
                    $input["types"] = $campaign->marketing_type;
                    $input["setting_id"] = empty($setting->id) ? 0 : $setting->id;
                    $input["setting_title"] = empty($setting->title) ? null : $setting->title;
                    $input["used_count"] = empty($setting->used_count) ? 0 : $setting->used_count;
                    $input["total_price"] = $total_price;
                    $input["check_out_price"] = $check_out_price;
                    $input["deduct_price"] = $deduct_price;

                    //記錄有統計資料之setting_id
                    if (!in_array($input["setting_id"], $setting_id_arr))
                    {
                        $setting_id_arr[] = $input["setting_id"];
                    }

                    $report_campaign_id_arr[] = $this->add_report_campaign($input);
                }
            }

            //取出無統計資料之campaign_setting資料
            $empty_settings = $campaign_settings->whereNotIn("id", $setting_id_arr)->get();

            if (!$store->isEmpty()) {
                foreach ($store as $val) {
                    if (!$empty_settings->isEmpty()) {
                        foreach ($empty_settings as $setting) {

                            $input = $this->campaign_format();

                            $input["company_id"] = $company_id;
                            $input["store_id"] = $val->id;
                            $input["years"] = date("Y", strtotime($start_datetime));
                            $input["months"] = date("m", strtotime($start_datetime));
                            $input["days"] = date("d", strtotime($start_datetime));
                            $input["hours"] = date("H", strtotime($start_datetime));
                            $input["start_hour"] = $start_at;
                            $input["end_hour"] = $end_at;

                            $input["types"] = $setting->types;
                            $input["setting_id"] = $setting->id;
                            $input["setting_title"] = $setting->title;

                            $this->add_report_campaign($input);
                        }
                    }
                }
            }
        }

        $this->info('campaign report executed!');
        $this->info('END');
    }


    //新增或更新Report_campaign資料
    private function add_report_campaign($input)
    {
        $db = Report_campaign::updateOrCreate(["company_id" => $input["company_id"],
            "store_id" => $input["store_id"], "years" => $input["years"],
            "months" => $input["months"], "days" => $input["days"],
            "hours" => $input["hours"], "start_hour" => $input["start_hour"],
            "end_hour" => $input["end_hour"], "types" => $input["types"],
            "setting_id" => $input["setting_id"], "setting_title" => $input["setting_title"]],
            ["used_count" => $input["used_count"], "total_price" => $input["total_price"],
                "check_out_price" => $input["check_out_price"], "deduct_price" => $input["deduct_price"]]);

        return $db->id;
    }

    private function campaign_format()
    {
        $format_array = array(
            "company_id"        => 0,
            "store_id"          => 0,
            "years"             => 0,
            "months"            => 0,
            "days"              => 0,
            "hours"             => 0,
            "start_hour"        => null,
            "end_hour"          => null,
            "types"             => 0,
            "setting_id"        => 0,
            "setting_title"     => null,
            "used_count"        => 0,
            "total_price"       => 0,
            "check_out_price"   => 0,
            "deduct_price"      => 0,
        );
        return $format_array;
    }

}