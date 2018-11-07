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
use App\Models\idelivery\Report_order;
use App\Models\idelivery\Report_order_detail;
use App\Models\idelivery\Report_campaign;
use App\Models\idelivery\Campaign_setting;
use App\Models\idelivery\Company;
use App\Models\idelivery\Store;
use App\Models\idelivery\Cuisine_group;
use App\Models\idelivery\Menu_item;

use DB;

/**
 * 愛外送 報表統計
 * php artisan Reports:ReportsGenJob
 */
class ReportsGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reports:ReportsGenJob {--company= : 品牌ID} {--start_datetime= : 查詢日EX=20180101010000}';
    //20180101010000 = 2018-01-01 01:00:00 ~ 2018-01-01 01:59:59

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '訂單報表統計';

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
            $start_datetime = date("Y-m-d H:i:s");
        }
        $this->info('start datetime:' . $start_datetime);

        //所有要執行的品牌
        foreach($company_arr as $company_id) {

            //取得起迄時段
            $start_hour = date("Y-m-d H", strtotime($start_datetime));
            $end_hour = $start_hour;

            $start_at = date("Y-m-d H:i:s", strtotime($start_hour.":00:00"));
            $end_at = date("Y-m-d H:i:s", strtotime($end_hour.":59:59"));


//        $start_at = "2018-05-01 00:00:01";
//        $end_at = "2018-07-31 23:59:59";

            /******************************************************************************/
            //訂單
            /******************************************************************************/
            //取得符合條件之訂單資料
            $orders_src = Order::where("company_id", '=', $company_id)
                ->whereNotNull("billing_day")//開帳日期不為空
                ->where(function ($query) {
                    $query->whereNotNull("billing_at")//接單時間不為空
                    ->orWhereNotNull("create_time");//訂單建立時間不為空
                })
                ->where("status", "=", "6")//完成單
                ->where("confirm_status", "!=", "3")//未取消接單
                ->whereBetween(DB::raw("case when billing_day is not null 
                    and billing_at is not null then concat(billing_day, ' ', time(billing_at))
                    when billing_day is not null and billing_at is null then concat(billing_day, ' ', time(create_time)) 
                    else null end"), [$start_at, $end_at])//符合起迄時段
                ->select("id", "company_id", "store_id", DB::raw("(case when billing_day is not null
                    and billing_at is not null then concat(billing_day, ' ', hour(billing_at))
                    when billing_day is not null and billing_at is null and create_time is not null 
                    then concat(billing_day, ' ', hour(create_time))
                    else null end) as report_at"),
                    "product_delivery", "payment", "member_id", "src_amount", "total_qty",
                    "discount_amount", "coupon_discount", "custom_discount", "amount");

            //取出符合條件之訂單編號
            $order_id_arr = $orders_src->pluck("id")->toArray();

            //無訂單接單時間時, 以訂單建立時間為統計時間
            $orders = $orders_src->select("company_id", "store_id", DB::raw("(case when billing_day is not null
                and billing_at is not null then concat(billing_day, ' ', hour(billing_at))
                when billing_day is not null and billing_at is null and create_time is not null 
                then concat(billing_day, ' ', hour(create_time))
                else null end) as report_at"),
                "product_delivery", "payment", "member_id", DB::raw("count(*) as order_count,
                SUM(src_amount) as src_amount,
                SUM(total_qty) as total_qty, SUM(discount_amount) as discount_amount,
                SUM(coupon_discount) as coupon_discount, SUM(custom_discount) as custom_discount,
                SUM(amount) as amount"))
                ->groupBy("company_id", "store_id", "report_at", "product_delivery", "payment",
                    "member_id")
                ->orderBy("company_id", "store_id")
                ->get();

            //取得品牌所屬店家資料
            $store_src = Store::where("company_id", $company_id);
            $store = $store_src->get();
            $store_id_arr = $store_src->pluck("id")->toArray();

            if (!$orders->isEmpty()) {
                //建立或更新排程區間訂單資料
                foreach ($orders as $order) {
                    $order_input = $this->order_format();

                    $order_input["company_id"] = $company_id;
                    $order_input["store_id"] = $order->store_id;
                    $order_input["years"] = date("Y", strtotime($start_datetime));
                    $order_input["months"] = date("m", strtotime($start_datetime));
                    $order_input["days"] = date("d", strtotime($start_datetime));
                    $order_input["hours"] = date("H", strtotime($start_datetime));
                    $order_input["start_hour"] = $start_at;
                    $order_input["end_hour"] = $end_at;
                    $order_input["product_delivery"] = $order->product_delivery;
                    $order_input["payment"] = $order->payment;
                    $order_input["member_class"] = 0;
                    $order_input["weather"] = 0;
                    $order_input["order_count"] = $order->order_count;
                    $order_input["src_amount"] = empty($order->src_amount) ? 0 : $order->src_amount;
                    $order_input["total_qty"] = empty($order->total_qty) ? 0 : $order->total_qty;
                    $order_input["discount_amount"] = empty($order->discount_amount) ? 0 : $order->discount_amount;
                    $order_input["coupon_discount"] = empty($order->coupon_discount) ? 0 : $order->coupon_discount;
                    $order_input["custom_discount"] = empty($order->custom_discount) ? 0 : $order->custom_discount;
                    $order_input["amount"] = empty($order->amount) ? 0 : $order->amount;

                    $report_order_id_arr[] = $this->add_report_order($order_input);
                }

                if (!$store->isEmpty()) {
                    $report_order = Report_order::whereIn("id", $report_order_id_arr)
                        ->get();

                    foreach ($store as $val) {
                        $order_input = $this->order_format();

                        $order_input["company_id"] = $company_id;
                        $order_input["store_id"] = $val->id;
                        $order_input["years"] = date("Y", strtotime($start_datetime));
                        $order_input["months"] = date("m", strtotime($start_datetime));
                        $order_input["days"] = date("d", strtotime($start_datetime));
                        $order_input["hours"] = date("H", strtotime($start_datetime));
                        $order_input["start_hour"] = $start_at;
                        $order_input["end_hour"] = $end_at;

                        //填補未符條件之資料
                        //訂單類型
                        for ($product_delivery = 1; $product_delivery <= 3; $product_delivery++) {
                            //付款方式
                            for ($payment = 1; $payment <= 2; $payment++) {
                                //判斷資料是否已新增
                                $found = $report_order->where("company_id", $company_id)
                                    ->where("store_id", $val->id)
                                    ->where("years", $order_input["years"])
                                    ->where("months", $order_input["months"])
                                    ->where("days", $order_input["days"])
                                    ->where("hours", $order_input["hours"])
                                    ->where("start_hour", $order_input["start_hour"])
                                    ->where("end_hour", $order_input["end_hour"])
                                    ->where("product_delivery", $product_delivery)
                                    ->where("payment", $payment)
                                    ->first();

                                if (empty($found)) {
                                    $order_input["product_delivery"] = $product_delivery;
                                    $order_input["payment"] = $payment;

                                    $this->add_report_order($order_input);
                                }
                            }
                        }
                    }
                }
            } else    //排程區間訂單資料為空, 則加入空資料
            {
                $order_input = $this->order_format();

                if (!$store->isEmpty()) {
                    foreach ($store as $val) {
                        $order_input["company_id"] = $company_id;
                        $order_input["store_id"] = $val->id;
                        $order_input["years"] = date("Y", strtotime($start_datetime));
                        $order_input["months"] = date("m", strtotime($start_datetime));
                        $order_input["days"] = date("d", strtotime($start_datetime));
                        $order_input["hours"] = date("H", strtotime($start_datetime));
                        $order_input["start_hour"] = $start_at;
                        $order_input["end_hour"] = $end_at;

                        //訂單類型
                        for ($product_delivery = 1; $product_delivery <= 3; $product_delivery++) {
                            //付款方式
                            for ($payment = 1; $payment <= 2; $payment++) {
                                $order_input["product_delivery"] = $product_delivery;
                                $order_input["payment"] = $payment;

                                $this->add_report_order($order_input);
                            }
                        }
                    }
                }
            }

            $this->info('order report executed!');

            /******************************************************************************/
            //訂單明細
            /******************************************************************************/
            //取得排程區間內訂單明細資料
            $order_details = Order_detail::join("order", "order.id", "=", "order_detail.order_id")
                ->join("menu_item", "menu_item.id", "=", "order_detail.item_id")
                ->leftjoin("cuisine_group", "cuisine_group.id", "=", "menu_item.group_id")
                ->whereIn("order.id", $order_id_arr)
                ->select("order.company_id", "order.store_id", DB::raw("case when order.billing_day is not null
                and order.billing_at is not null then concat(order.billing_day, ' ', hour(order.billing_at))
                when order.billing_day is not null and order.billing_at is null then
                concat(order.billing_day, ' ', hour(order.create_time)) else null end as report_at"),
                    "menu_item.group_id", "cuisine_group.group_name", "order_detail.item_id",
                    "order_detail.item_name",
                    DB::raw("SUM(order_detail.item_price) as item_price,
                SUM(order_detail.qty) as qty, 0 as discount_amount,
                SUM(order_detail.sub_price) as sub_price"))
                ->groupBy("order.company_id", "order.store_id", "report_at", "menu_item.group_id",
                    "cuisine_group.group_name", "order_detail.item_id", "order_detail.item_name")
                ->orderBy("order.company_id", "order.store_id")
                ->get();

            //取得品牌及店家餐點分類
            $groups = Cuisine_group::where("company_id", $company_id)
                ->where("store_id", 0)
                ->where("status", 1)  
                ->orWhere(function ($query) use ($company_id) {
                    $query->where("company_id", $company_id)
                        ->where("store_id", "!=", 0);
                })
                ->get();

            //取得品牌及店家餐點品項
            $items = Menu_item::join("menu_store_item", "menu_store_item.item_id",
                "menu_item.id")
                ->where("menu_item.company_id", $company_id)
                ->where("menu_item.store_id", 0)
                ->where("menu_item.status", 1)
                ->orWhere(function ($query) use ($store_id_arr) {
                    $query->where("menu_store_item.status", 1)
                        ->whereIn("menu_store_item.store_id", $store_id_arr);
                })
                ->get();

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

                    $report_order_detai_id_arr[] = $this->add_report_order_detail($detail_input);
                }

                if (!$store->isEmpty()) {
                    $detail_input = $this->order_detail_format();
                    $report_order_detail = Report_order_detail::whereIn("id", $report_order_detai_id_arr)
                        ->get();

                    foreach ($store as $val) {
                        $detail_input["company_id"] = $company_id;
                        $detail_input["store_id"] = $val->id;
                        $detail_input["years"] = date("Y", strtotime($start_datetime));
                        $detail_input["months"] = date("m", strtotime($start_datetime));
                        $detail_input["days"] = date("d", strtotime($start_datetime));
                        $detail_input["hours"] = date("H", strtotime($start_datetime));
                        $detail_input["start_hour"] = $start_at;
                        $detail_input["end_hour"] = $end_at;

                        if (!$groups->isEmpty() && !$items->isEmpty()) {
                            //填補未符條件之資料
                            foreach ($groups as $group) {
                                foreach ($items as $item) {
                                    $found = $report_order_detail->where("company_id", $company_id)
                                        ->where("store_id", $val->id)
                                        ->where("years", $detail_input["years"])
                                        ->where("months", $detail_input["months"])
                                        ->where("days", $detail_input["days"])
                                        ->where("hours", $detail_input["hours"])
                                        ->where("start_hour", $detail_input["start_hour"])
                                        ->where("end_hour", $detail_input["end_hour"])
                                        ->where("group_id", $group->id)
                                        ->where("item_id", $item->id)
                                        ->first();

                                    if (empty($found)) {
                                        $detail_input["group_id"] = $group->id;
                                        $detail_input["group_name"] = $group->group_name;
                                        $detail_input["item_id"] = $item->id;
                                        $detail_input["item_name"] = $item->name;

                                        $this->add_report_order_detail($detail_input);
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
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

                        foreach ($groups as $group) {
                            foreach ($items as $item) {
                                $detail_input["group_id"] = $group->id;
                                $detail_input["group_name"] = $group->group_name;
                                $detail_input["item_id"] = $item->id;
                                $detail_input["item_name"] = $item->name;

                                $this->add_report_order_detail($detail_input);
                            }
                        }
                    }
                }
            }

            $this->info('order_detail report executed!');

            /******************************************************************************/
            //折扣類型
            /******************************************************************************/
            $campaigns = Order::leftJoin("campaign_log", "campaign_log.order_id", "order.id")
                ->leftJoin("coupon_log", "coupon_log.order_id", "order.id")
                ->whereIn("order.id", $order_id_arr)
                ->where("order.marketing_type", "!=", 0)
                ->whereNotNull("order.marketing_sn")
                ->select("order.company_id", "order.store_id", DB::raw("case when order.billing_day is not null 
                and order.billing_at is not null then concat(order.billing_day, ' ', hour(order.billing_at))
                when order.billing_day is not null and order.billing_at is null then 
                concat(order.billing_day, ' ', hour(order.create_time)) else null end as report_at"),
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
                ->get();

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
                        $deduct_price = empty($campaign->coupon_deduct)  ? 0 : $campaign->coupon_deduct;
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
                    $input["setting_id"] = empty($setting->id) ? 0: $setting->id;
                    $input["setting_title"] = empty($setting->title) ? null : $setting->title;
                    $input["used_count"] = empty($setting->used_count) ? 0 : $setting->used_count;
                    $input["total_price"] = $total_price;
                    $input["check_out_price"] = $check_out_price;
                    $input["deduct_price"] = $deduct_price;

                    $report_campaign_id_arr[] = $this->add_report_campaign($input);
                }

                if (!$store->isEmpty()) {
                    $report_campaign = Report_campaign::whereIn("id", $report_campaign_id_arr)
                        ->get();

                    foreach ($store as $val) {
                        if (!$campaign_settings->isEmpty()) {
                            foreach ($campaign_settings as $setting) {
                                $input = $this->campaign_format();

                                $input["company_id"] = $company_id;
                                $input["store_id"] = $val->id;
                                $input["years"] = date("Y", strtotime($start_datetime));
                                $input["months"] = date("m", strtotime($start_datetime));
                                $input["days"] = date("d", strtotime($start_datetime));
                                $input["hours"] = date("H", strtotime($start_datetime));
                                $input["start_hour"] = $start_at;
                                $input["end_hour"] = $end_at;

                                $found = $report_campaign->where("company_id", $company_id)
                                    ->where("store_id", $val->id)
                                    ->where("years", $input["years"])
                                    ->where("months", $input["months"])
                                    ->where("days", $input["days"])
                                    ->where("hours", $input["hours"])
                                    ->where("start_hour", $input["start_hour"])
                                    ->where("end_hour", $input["end_hour"])
                                    ->where("setting_id", $setting->id)
                                    ->first();

                                if (empty($found)) {
                                    $input["types"] = $setting->types;
                                    $input["setting_id"] = $setting->id;
                                    $input["setting_title"] = $setting->title;

                                    $this->add_report_campaign($input);
                                }
                            }
                        }
                    }
                }
            } else {
                if (!$store->isEmpty()) {
                    foreach ($store as $val) {
                        if (!empty($campaign_settings)) {
                            foreach ($campaign_settings as $setting) {
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

                                Report_campaign::updateOrCreate($input);
                            }
                        }
                    }
                }
            }

        }
        $this->info('campaign report executed!');
        $this->info('END');
    }


    //新增或更新Report_order資料
    private function add_report_order($order_input)
    {
        $db = Report_order::updateOrCreate(["company_id" => $order_input["company_id"],
            "store_id" => $order_input["store_id"], "years" => $order_input["years"],
            "months" => $order_input["months"], "days" => $order_input["days"],
            "hours" => $order_input["hours"], "start_hour" => $order_input["start_hour"],
            "end_hour" => $order_input["end_hour"], "product_delivery" => $order_input["product_delivery"],
            "payment" => $order_input["payment"], "member_class" => $order_input["member_class"],
            "weather" => $order_input["weather"]],
            ["order_count" => $order_input["order_count"],
                "src_amount" => $order_input["src_amount"], "total_qty" => $order_input["total_qty"],
                "discount_amount" => $order_input["discount_amount"], "coupon_discount" => $order_input["coupon_discount"],
                "custom_discount" => $order_input["custom_discount"], "amount" => $order_input["amount"]]);

        return $db->id;
    }

    //新增或更新Report_order資料
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

    private function order_format()
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
            "product_delivery"  => 0,
            "payment"           => 0,
            "member_class"      => 0,
            "weather"           => 0,
            "order_count"       => 0,
            "src_amount"        => 0,
            "total_qty"         => 0,
            "discount_amount"   => 0,
            "coupon_discount"   => 0,
            "custom_discount"   => 0,
            "amount"            => 0,
        );
        return $format_array;
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