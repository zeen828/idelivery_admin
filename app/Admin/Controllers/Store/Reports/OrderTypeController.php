<?php

namespace App\Admin\Controllers\Store\Reports;

use App\Models\idelivery\Report_order;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use DB;

class OrderTypeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('訂單類型統計表');
            $content->description('Order Type Report');

            $route = "/admin/store/reports/order_type/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

        });
    }


    public function search()
    {
        return Admin::content(function (Content $content) {

            $content->header('訂單類型統計表');
            $content->description('Order Type Report');

            $route = "/admin/store/reports/order_type/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

            $msg   = '查詢結果';
            $style = 'primary';

            if (!empty(request()->all()))
            {
                $start_at             = request()->query('start_date', date("Y-m-d 00:00:00"));
                $end_at               = request()->query('end_date', date("Y-m-d 23:59:59"));
                $str_token              = request()->_token;

                if ($start_at > $end_at)
                {
                    admin_toastr('日期或時間設定錯誤','warning');
                    return back();
                }

                //限制只能查詢三個月區間
                $datetime1 = new \DateTime($start_at);
                $datetime2 = new \DateTime($end_at);
                $interval = $datetime2->diff($datetime1);
                if (($interval->format("%m") >= 3 && $interval->format("%d") > 0)
                    || $interval->format("%y") != 0)
                {
                    admin_toastr('查詢區間限三個月', 'warning');
                    return back();
                }

                //載入餐點品項銷售統計資料
                $rows = $this->load_data($store_id, $start_at, $end_at);

                if (!empty($rows))
                {
                    $style = 'success';
                }
                else
                {
                    $rows  = [];
                    $msg   = '查無相關資料';
                    $style = 'danger';
                }

                $content->row(new Box('統計表', view('admin.reports.order_type_chart'), ['start_at'=>$start_at, 'end_at'=>$end_at, 'rows'=>$rows]));
                $sum = $this->sum_data($store_id, $start_at, $end_at);
                $box = new Box($msg, view('admin.reports.order_type_table', ['rows'=>$rows, 'sum'=>$sum]));
                $content->body($box->style($style)->solid());

            }

        });
    }

    //載入訂單類型統計資料
    private function load_data($store_id, $start_at, $end_at)
    {
        $orders = Report_order::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id);

        $amount = $orders->sum("src_amount");
        $order_count = $orders->sum("order_count");

        $rows = $orders->select(DB::raw("case when product_delivery = 1 then '外送'
                when product_delivery = 2 then '外帶' when product_delivery = 3 then '內用' 
                else '' end as product_delivery, 
                SUM(order_count) as qty,
                ROUND(SUM(order_count)*100/".$order_count.", 2) as order_percent,  
                SUM(src_amount) as src_amount, 
                ROUND(SUM(src_amount)*100/".$amount.", 2) as amount_percent, 
                ROUND(SUM(src_amount)/SUM(order_count), 0) as avg_price"))
            ->groupBy('product_delivery')
            ->get();

        return $rows;
    }

    public function load_count_data()
    {
        if (!empty(request()->all()))
        {
            $start_date = request()->query('start_date', date("Y-m-d"));
            $end_date = request()->query('end_date', date("Y-m-d"));
            $start_time = request()->query('start_time', '00:00:00');
            $end_time = request()->query('end_time', '23:59:59');

            $start_at = $start_date.' '.$start_time;
            $end_at = $end_date.' '.$end_time;

            if ($start_at > $end_at)
            {
                response()->false;
            }

            $store_id = Session::get('store_id');

            $rows = $this->load_data($store_id, $start_at, $end_at);

            $dataSeries = array();

            if (!empty($rows))
            {
                foreach ($rows as $row)
                {
                    $obj = new \StdClass();
                    $obj->name = $row->product_delivery;
                    $obj->data = (int) $row->qty;
                    $obj->y = (float) $row->order_percent;
                    $obj->avg_name = "平均客單價";
                    $obj->avg = (int) $row->avg_price;

                    $dataSeries[] = $obj;
                }
            }

            return response()->json([
                'title' => '訂單數統計',
                'sub_title' => '訂單數',
                'dataSeries' => $dataSeries,
            ]);
        }
    }

    public function load_amount_data()
    {
        if (!empty(request()->all()))
        {
            $start_date = request()->query('start_date', date("Y-m-d"));
            $end_date = request()->query('end_date', date("Y-m-d"));
            $start_time = request()->query('start_time', '00:00:00');
            $end_time = request()->query('end_time', '23:59:59');

            $start_at = $start_date.' '.$start_time;
            $end_at = $end_date.' '.$end_time;

            if ($start_at > $end_at)
            {
                response()->false;
            }

            $store_id = Session::get('store_id');

            $orders = Report_order::where('start_hour', '>=', $start_at)
                ->where('end_hour', '<=', $end_at)
                ->where('store_id', $store_id);

            $amount = $orders->sum("src_amount");

            $rows = $orders->select(DB::raw("case when product_delivery = 1 then '外送'
                when product_delivery = 2 then '外帶' when product_delivery = 3 then '內用'
                else '' end as product_delivery,
                SUM(src_amount) as src_amount,
                ROUND(SUM(src_amount)*100/".$amount.", 2) as amount_percent,
                ROUND(SUM(src_amount)/SUM(order_count), 0) as avg_price"))
                ->groupBy('product_delivery')
                ->get();

            $dataSeries = array();

            foreach ($rows as $row)
            {
                $obj = new \StdClass();
                $obj->name = $row->product_delivery;
                $obj->data = (int) $row->src_amount;
                $obj->y = (float) $row->amount_percent;
                $obj->avg_name = "平均客單價";
                $obj->avg = (int) $row->avg_price;

                $dataSeries[] = $obj;
            }

            return response()->json([
                'title' => '銷售額統計',
                'sub_title' => '銷售額',
                'dataSeries' => $dataSeries,
            ]);

        }
    }


    //統計訂單類型統計資料
    private function sum_data($store_id, $start_at, $end_at)
    {
        $orders = Report_order::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id);

        $amount = $orders->sum("src_amount");
        $order_count = $orders->sum("order_count");

        $rows = $orders->select(DB::raw("SUM(order_count) as qty,
                ROUND(SUM(order_count)*100/".$order_count.", 2) as order_percent,  
                SUM(src_amount) as src_amount, 
                ROUND(SUM(src_amount)*100/".$amount.", 2) as amount_percent, 
                ROUND(SUM(src_amount)/SUM(order_count), 0) as avg_price"))
            ->get();

        return $rows;
    }
}
