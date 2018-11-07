<?php
/**
 * Created by PhpStorm.
 * User: cecyu
 * Date: 2018/9/10
 * Time: 上午 10:25
 */

namespace App\Console\Reports;

// 核心
use Illuminate\Console\Command;

use App\Models\system_damaiapp\Company as DamaiappCompany;
use App\Models\idelivery\Billing;
use App\Models\idelivery\Billing_detail;
use App\Models\idelivery\Report_order_detail;
use App\Models\idelivery\Company;
use App\Models\idelivery\Store;
use App\Models\idelivery\Menu_store_item;

use DB;

/**
 * 愛外送 報表統計
 * php artisan Reports:ReportBillingDetailGenJob --company=3 --start_datetime=20180808010000
 */
class ReportBillingDetailGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reports:ReportBillingDetailGenJob {--company= : 品牌ID} {--start_datetime= : 查詢日EX=20180101010000}';
    //20180101010000 = 2018-01-01 01:00:00 ~ 2018-01-01 01:59:59

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '結帳單明細報表統計';

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

            $start_at = date("Y-m-d H:i:s", strtotime($start_hour . ":00:00"));
            $end_at = date("Y-m-d H:i:s", strtotime($end_hour . ":59:59"));

            /******************************************************************************/
            //結帳單明細
            /******************************************************************************/

            //取得符合條件之訂單資料
            $billing_src = Billing::where("service_id", env("SERVICE_ID"))
                ->where("company_id", '=', $company_id)
                ->whereNotNull("created_time")
                ->where("status", 1)
                ->whereBetween("created_time", [$start_at, $end_at]);//符合起迄時段

            //取出符合條件之訂單編號
            $billing_id_arr = $billing_src->pluck("id")->toArray();

            //取得品牌所屬店家資料
            $store_src = Store::where("company_id", $company_id);
            $store = $store_src->get();
            $store_id_arr = $store_src->pluck("id")->toArray();

            //取得排程區間內訂單明細資料
            $billing_details = Billing_detail::join("billing", "billing.id", "=", "billing_detail.billing_id")
                ->whereIn("billing.id", $billing_id_arr)
                ->select("billing.company_id", "billing.store_id", DB::raw("concat(date(billing.created_time), 
                    ' ', hour(billing.created_time)) as report_at, billing_detail.item_id,
                    SUM(billing_detail.item_price) as item_price, SUM(billing_detail.qty) as qty, 
                    SUM(billing_detail.sub_price) as sub_price"))
                ->groupBy("billing.company_id", "billing.store_id", "report_at", "billing_detail.item_id")
                ->orderBy("billing.company_id", "billing.store_id")
                ->get();

            //取得店家餐點品項
            $items_src = Menu_store_item::where("status", 1)
                ->whereIn("store_id", $store_id_arr);

            $item_id_arr = array();
            if (!empty($billing_details)) {
                //新增訂單明細排程區間統計資料
                foreach ($billing_details as $detail) {
                    $detail_input = $this->order_detail_format();
                    $detail_input["company_id"] = $detail->company_id;
                    $detail_input["store_id"] = $detail->store_id;
                    $detail_input["years"] = date("Y", strtotime($start_datetime));
                    $detail_input["months"] = date("m", strtotime($start_datetime));
                    $detail_input["days"] = date("d", strtotime($start_datetime));
                    $detail_input["hours"] = date("H", strtotime($start_datetime));
                    $detail_input["start_hour"] = $start_at;
                    $detail_input["end_hour"] = $end_at;
                    $menu_item = $detail->menu_item;
                    $detail_input["group_id"] = empty($menu_item->group_id) ? 0 : $menu_item->group_id;
                    $detail_input["group_name"] = $menu_item->group->group_name;
                    $detail_input["item_id"] = $detail->item_id;
                    $detail_input["item_name"] = $menu_item->name;
                    $detail_input["item_price"] = $detail->item_price;
                    $detail_input["qty"] = $detail->qty;
                    //$detail_input["discount_amount"] = $detail->discount_amount;
                    $detail_input["sub_price"] = $detail->sub_price;

                    $item_id_arr[] = $detail->item_id;

                    $report_order_detai_id_arr[] = $this->add_report_order_detail($detail_input);
                }
            }

            $items = $items_src->whereNotIn("item_id", $item_id_arr)->get();

            if (!empty($store)) {
                $detail_input = $this->order_detail_format();

                foreach ($store as $val) {
                    $detail_input["company_id"] = $company_id;
                    $detail_input["store_id"] = $val->id;
                    $detail_input["years"] = date("Y", strtotime($start_datetime));
                    $detail_input["months"] = date("m", strtotime($start_datetime));
                    $detail_input["days"] = date("d", strtotime($start_datetime));
                    $detail_input["hours"] = date("H", strtotime($start_datetime));
                    $detail_input["start_hour"] = $start_at;
                    $detail_input["end_hour"] = $end_at;

                    if (!empty($items)) {
                        //填補未符條件之資料
                        foreach ($items as $item) {
                            $menu_item = $item->menu_item;
                            if (count($menu_item) > 0)
                            {
                                $detail_input["group_id"] = empty($menu_item->group_id) ? 0 : $menu_item->group_id;
                                $detail_input["group_name"] = empty($menu_item->group->group_name) ? null : $menu_item->group->group_name;
                                $detail_input["item_id"] = $item->item_id;
                                $detail_input["item_name"] = empty($menu_item->name) ? null : $menu_item->name;

                                $this->add_report_order_detail($detail_input);
                            }
                        }
                    }
                }
            }
        }

        $this->info('order_detail report executed!');
        $this->info('END');
    }


    //新增或更新Report_order_detail資料
    private function add_report_order_detail($detail_input)
    {
        $db = Report_order_detail::updateOrCreate(["company_id" => $detail_input["company_id"],
            "store_id" => $detail_input["store_id"], "years" => $detail_input["years"],
            "months" => $detail_input["months"], "days" => $detail_input["days"],
            "hours" => $detail_input["hours"], "start_hour" => $detail_input["start_hour"],
            "end_hour" => $detail_input["end_hour"], "group_id" => $detail_input["group_id"],
            "item_id" => $detail_input["item_id"]
            ],
            ["item_price" => $detail_input["item_price"], "qty" => $detail_input["qty"],
                "discount_amount" => $detail_input["discount_amount"],
                "sub_price" => $detail_input["sub_price"], "group_name" => $detail_input["group_name"],
                "item_name" => $detail_input["item_name"]]);

        return $db->id;
    }


    private function order_detail_format()
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
            "group_id"          => 0,
            "group_name"        => null,
            "item_id"           => 0,
            "item_name"         => null,
            "item_price"        => 0,
            "qty"               => 0,
            "discount_amount"   => 0,
            "sub_price"         => 0,
        );
        return $format_array;
    }


}