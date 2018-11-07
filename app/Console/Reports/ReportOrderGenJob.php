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
 * php artisan Reports:ReportOrderGenJob --company=3 --start_datetime=20180808010000
 */
class ReportOrderGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Reports:ReportOrderGenJob {--company= : 品牌ID} {--start_datetime= : 查詢日EX=20180101010000}';
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
            //訂單
            /******************************************************************************/
            //取得符合條件之訂單資料(2018-08-27 改以訂單建立時間查詢)
            $orders_src = Order::where("company_id", '=', $company_id)
                ->whereNotNull("create_time")//開帳日期不為空
                ->whereNotIn("status", ["1", "7"])//不為未接單及取消接單
                //->where("confirm_status", "!=", "3")//不為取消接單
                ->whereBetween("create_time", [$start_at, $end_at])//符合起迄時段
                ->select("id", "company_id", "store_id", DB::raw("concat(date(create_time),
                    ' ', hour(create_time)) as report_at"),
                    "product_delivery", "payment", "member_id", "src_amount", "total_qty",
                    "discount_amount", "coupon_discount", "custom_discount", "amount");

            $orders = $orders_src->select("company_id", "store_id", DB::raw("concat(date(create_time), 
                ' ', hour(create_time)) as report_at"),
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

            //定義取貨方式及付款方式
            $key_arr = array();

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

                    //記錄有統計資料之取貨方式及付款方式
                    $obj = new \StdClass();
                    $obj->product_delivery = $order->product_delivery;
                    $obj->payment = $order->payment;

                    $key_arr[] = $obj;

                    $this->add_report_order($order_input);
                }
            }

            //建立所有取貨方式及付款方式之組合
            $key_list = array();
            for ($product_delivery = 1; $product_delivery <= 3; $product_delivery++)
            {
                //付款方式
                for ($payment = 1; $payment <= 2; $payment++)
                {
                    $obj = new \StdClass();
                    $obj->product_delivery = $product_delivery;
                    $obj->payment = $payment;

                    $key_list[] = $obj;
                }
            }

            //取出無統計資料之取貨方式及付款方式
            if (count($key_arr) > 0)
            {
                foreach ($key_list as $key => $list_val)
                {
                    foreach ($key_arr as $val)
                    {
                        if ($list_val->product_delivery == $val->product_delivery
                            && $list_val->payment == $val->payment)
                        {
                            unset($key_list[$key]);
                        }
                    }
                }
            }

            if (!$store->isEmpty())
            {
                foreach ($store as $val)
                {
                    if (count($key_list) > 0)
                    {
                        $order_input = $this->order_format();

                        $order_input["company_id"] = $company_id;
                        $order_input["store_id"] = $val->id;
                        $order_input["years"] = date("Y", strtotime($start_datetime));
                        $order_input["months"] = date("m", strtotime($start_datetime));
                        $order_input["days"] = date("d", strtotime($start_datetime));
                        $order_input["hours"] = date("H", strtotime($start_datetime));
                        $order_input["start_hour"] = $start_at;
                        $order_input["end_hour"] = $end_at;

                        foreach ($key_list as $val)
                        {
                            $order_input["product_delivery"] = $val->product_delivery;
                            $order_input["payment"] = $val->payment;

                            $this->add_report_order($order_input);
                        }
                    }
                }
            }
        }

        $this->info('order report executed!');
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

}