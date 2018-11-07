<?php

namespace App\Admin\Controllers\Store\Reports;

use App\Models\idelivery\Report_order;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use DB;

class PaymentController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('付款方式統計表');
            $content->description('Payment Type Report');

            $route = "/admin/store/reports/payment/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

        });
    }


    public function search()
    {
        return Admin::content(function (Content $content) {

            $content->header('付款方式統計表');
            $content->description('Payment Type Report');

            $route = "/admin/store/reports/payment/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

            $msg   = '查詢結果';
            $style = 'primary';

            if (!empty(request()->all())) {
                $start_at             = request()->query('start_date', date("Y-m-d 00:00:00"));
                $end_at               = request()->query('end_date', date("Y-m-d 23:59:59"));
                $str_token = request()->_token;

                if ($start_at > $end_at) {
                    admin_toastr('日期或時間設定錯誤', 'warning');
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

                if (!$rows->isEmpty())
                {
                    $style = 'success';
                }
                else
                {
                    $rows  = [];
                    $msg   = '查無相關資料';
                    $style = 'danger';
                }

                $content->row(new Box('統計表', view('admin.reports.pay_method_chart'), ['start_at'=>$start_at, 'end_at'=>$end_at, 'rows'=>$rows]));

                $box = new Box($msg, view('admin.reports.pay_method_table', ['rows'=>$rows]));

                $content->body($box->style($style)->solid());

            }
        });
    }

    //載入付款方式統計資料
    private function load_data($store_id, $start_at, $end_at)
    {
        $orders = Report_order::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id);

        $src_amount = $orders->sum("src_amount");
        $amount = $orders->sum("amount");

        $rows = $orders->select(DB::raw("case when payment = 1 then '現金'
                when payment = 2 then '信用卡' else '' end as payment, 
                SUM(src_amount) as src_amount,
                ROUND(SUM(src_amount)*100/".$src_amount.", 2) as src_percent,
                SUM(amount) as amount,
                ROUND(SUM(amount)*100/".$amount.", 2) as percent"))
            ->groupBy(DB::raw('payment WITH ROLLUP'))
            ->get();

        return $rows;
    }

    //載入付款方式統計資料
    public function load_src_amount_data()
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

            $src_amount = $orders->sum("src_amount");

            $rows = $orders->select(DB::raw("case when payment = 1 then '現金'
                when payment = 2 then '信用卡' else '' end as payment, 
                SUM(src_amount) as src_amount,
                ROUND(SUM(src_amount)*100/".$src_amount.", 2) as percent"))
                ->groupBy('payment')
                ->get();

            $dataSeries = array();

            foreach ($rows as $row)
            {
                $obj = new \StdClass();
                $obj->name = $row->payment;
                $obj->data = (int) $row->src_amount;
                $obj->y = (float) $row->percent;

                $dataSeries[] = $obj;
            }

            return response()->json([
                'title' => '銷售金額統計',
                'sub_title' => '銷售額',
                'dataSeries' => $dataSeries,
            ]);

        }
    }

    //載入付款方式統計資料
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

            $amount = $orders->sum("amount");

            $rows = $orders->select(DB::raw("case when payment = 1 then '現金'
                when payment = 2 then '信用卡' else '' end as payment, 
                SUM(amount) as amount,
                ROUND(SUM(amount)*100/".$amount.", 2) as percent"))
                ->groupBy('payment')
                ->get();

            $dataSeries = array();

            foreach ($rows as $row)
            {
                $obj = new \StdClass();
                $obj->name = $row->payment;
                $obj->data = (int) $row->amount;
                $obj->y = (float) $row->percent;

                $dataSeries[] = $obj;
            }

            return response()->json([
                'title' => '付款金額統計',
                'sub_title' => '付款金額',
                'dataSeries' => $dataSeries,
            ]);

        }
    }
}
