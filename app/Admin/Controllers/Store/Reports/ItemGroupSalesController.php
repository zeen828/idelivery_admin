<?php

namespace App\Admin\Controllers\Store\Reports;

use App\Models\idelivery\Report_order_detail;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use DB;

class ItemGroupSalesController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('餐點分類銷售統計表');
            $content->description('Cuisine Category Sale Report');

            $route = "/admin/store/reports/group_sale/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

        });

    }

    public function search()
    {
        return Admin::content(function (Content $content) {

            $content->header('餐點分類銷售統計表');
            $content->description('Cuisine Category Sale Report');

            $route = "/admin/store/reports/group_sale/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

            $msg   = '查詢結果';
            $style = 'primary';

            if (!empty(request()->all()))
            {
                $start_at             = request()->query('start_date', date("Y-m-d 00:00:00"));
                $end_at               = request()->query('end_date', date("Y-m-d 23:59:59"));
                //$str_token            = request()->_token;

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

                $content->row(new Box('統計表', view('admin.reports.item_group_sale_chart'), ['start_at'=>$start_at, 'end_at'=>$end_at, 'rows'=>$rows]));
                $sum = $this->sum_data($store_id, $start_at, $end_at);
                $box = new Box($msg, view('admin.reports.item_group_sale_table', ['rows'=>$rows, 'sum'=>$sum]));
                $content->body($box->style($style)->solid());

            }

        });

    }

    //載入餐點分類銷售統計資料
    private function load_data($store_id, $start_at, $end_at)
    {
        $orders = Report_order_detail::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id);

        $amount = $orders->sum("sub_price");

        $order_details = $orders->select("group_name", DB::raw("SUM(qty) as qty, 
                SUM(sub_price) as sub_price, 
                ROUND(SUM(sub_price)*100/".$amount.", 2) as percent , 
                0 as costs, SUM(sub_price) as profits"))
            ->groupBy('group_name')
            ->get();

        return $order_details;
    }

    //載入餐點分類銷售統計圖表資料
    public function load_chart_data()
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

            $order_details = $this->load_data($store_id, $start_at, $end_at);

            $dataSeries = array();
            $dataColumn = array('銷售總額', '預估毛利額');

            if (!empty($order_details))
            {
                foreach ($order_details as $row)
                {
                    $obj = new \StdClass();

                    $obj->name = $row->group_name;
                    $obj->data = array(
                        (int) $row->sub_price,
                        (int) $row->profits,
                    );
                    $dataSeries[] = $obj;
                }
            }

            return response()->json([
                'title' => '餐點分類銷售統計',
                'sidetitle' => '金額',
                'bottomtitle' => '分類',
                'dataColumn' => $dataColumn,
                'dataSeries' => $dataSeries,
            ]);
        }

    }


    //統計餐點分類銷售統計資料
    private function sum_data($store_id, $start_at, $end_at)
    {
        $orders = Report_order_detail::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id);

        $amount = $orders->sum("sub_price");

        $order_details = $orders->select(DB::raw("SUM(qty) as qty, 
                SUM(sub_price) as sub_price, 
                ROUND(SUM(sub_price)*100/".$amount.", 2) as percent , 
                0 as costs, SUM(sub_price) as profits"))
            ->get();

        return $order_details;
    }

}
