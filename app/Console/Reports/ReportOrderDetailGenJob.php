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
use App\Models\idelivery\Order_detail;
use App\Models\idelivery\Report_order_detail;
use App\Models\idelivery\Company;
use App\Models\idelivery\Store;
use App\Models\idelivery\Menu_item;

use DB;

/**
 * 愛外送 報表統計
 * php artisan Reports:ReportOrderDetailGenJob --company=3 --start_datetime=20180808010000
 */
class ReportOrderDetailGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reports:ReportOrderDetailGenJob {--company= : 品牌ID} {--start_datetime= : 查詢日EX=20180101010000}';
    //20180101010000 = 2018-01-01 01:00:00 ~ 2018-01-01 01:59:59

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '訂單明細報表統計';

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
            //訂單明細
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
            $store_id_arr = $store_src->pluck("id")->toArray();

            //取得排程區間內訂單明細資料
            $order_details = Order_detail::join("order", "order.id", "=", "order_detail.order_id")
                ->join("menu_item", "menu_item.id", "=", "order_detail.item_id")
                ->join("cuisine_group", "cuisine_group.id", "=", "menu_item.group_id")
                ->whereIn("order.id", $order_id_arr)
                ->select("order.company_id", "order.store_id", DB::raw("concat(date(order.create_time), 
                    ' ', hour(order.create_time)) as report_at"),
                    "menu_item.group_id", "cuisine_group.group_name", "order_detail.item_id",
                    "order_detail.item_name",
                    DB::raw("SUM(order_detail.item_price) as item_price,
                    SUM(order_detail.qty) as qty, 0 as discount_amount,
                    SUM(order_detail.sub_price) as sub_price"))
                ->groupBy("order.company_id", "order.store_id", "report_at", "menu_item.group_id",
                    "cuisine_group.group_name", "order_detail.item_id", "order_detail.item_name")
                ->orderBy("order.company_id", "order.store_id")
                ->get();

            //取得店家餐點品項
            $items_src = Menu_item::join("menu_store_item", "menu_store_item.item_id",
                "=", "menu_item.id")
                ->where("menu_item.company_id", $company_id)
                ->where("menu_store_item.status", 1)
                ->whereIn("menu_store_item.store_id", $store_id_arr)
                ->select("menu_item.*");

            $item_id_arr = array();
            if (!$order_details->isEmpty()) {
                //新增訂單明細排程區間統計資料
                foreach ($order_details as $detail) {
                    $detail_input = $this->order_detail_format();
                    $detail_input["company_id"] = $detail->company_id;
                    $detail_input["store_id"] = $detail->store_id;
                    $detail_input["years"] = date("Y", strtotime($start_datetime));
                    $detail_input["months"] = date("m", strtotime($start_datetime));
                    $detail_input["days"] = date("d", strtotime($start_datetime));
                    $detail_input["hours"] = date("H", strtotime($start_datetime));
                    $detail_input["start_hour"] = $start_at;
                    $detail_input["end_hour"] = $end_at;
                    $detail_input["group_id"] = empty($detail->group_id) ? 0 : $detail->group_id;
                    $detail_input["group_name"] = $detail->group_name;
                    $detail_input["item_id"] = $detail->item_id;
                    $detail_input["item_name"] = $detail->item_name;
                    $detail_input["item_price"] = $detail->item_price;
                    $detail_input["qty"] = $detail->qty;
                    $detail_input["discount_amount"] = $detail->discount_amount;
                    $detail_input["sub_price"] = $detail->sub_price;

                    $item_id_arr[] = $detail->item_id;

                    $report_order_detai_id_arr[] = $this->add_report_order_detail($detail_input);
                }
            }

            $items = $items_src->whereNotIn("menu_item.id", $item_id_arr)->get();

            if (!$store->isEmpty()) {
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

                    if (!$items->isEmpty()) {
                        //填補未符條件之資料
                        foreach ($items as $item) {
                            $detail_input["group_id"] = $item->group_id;
                            $detail_input["group_name"] = $item->cuisine_group->group_name;
                            $detail_input["item_id"] = $item->id;
                            $detail_input["item_name"] = $item->name;

                            $this->add_report_order_detail($detail_input);
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
            "group_name" => $detail_input["group_name"], "item_id" => $detail_input["item_id"],
            "item_name" => $detail_input["item_name"]],
            ["item_price" => $detail_input["item_price"],
                "qty" => $detail_input["qty"], "discount_amount" => $detail_input["discount_amount"],
                "sub_price" => $detail_input["sub_price"]]);

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