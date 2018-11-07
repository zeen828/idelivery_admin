<?php

namespace App\Admin\Controllers\Store\Reports;

use App\Models\idelivery\Report_order;

use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use DB;

class DailySalesController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $content->header('日期銷售統計表');
            $content->description('Daily Sales Report');

            $route = "/admin/store/reports/daily_sale/search";
            $content->body(new Box('查詢條件', view('admin.reports.query', ['route'=>$route])));

            $store_id = Session::get('store_id');

        });

    }


    public function search()
    {

        return Admin::content(function (Content $content) {

            $content->header('日期銷售統計表');
            $content->description('Daily Sales Report');

            $route = "/admin/store/reports/daily_sale/search";
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

                //載入日期銷售統計資料
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

                $content->row(new Box('統計表', view('admin.reports.daily_sale_chart'), ['start_at'=>$start_at, 'end_at'=>$end_at, 'rows'=>$rows]));
                $sum = $this->sum_data($store_id, $start_at, $end_at);
                $box = new Box($msg, view('admin.reports.daily_sale_table', ['rows'=>$rows, 'sum'=>$sum]));
                $content->body($box->style($style)->solid());
            }

        });

    }

    //載入日期銷售統計資料
    private function load_data($store_id, $start_at, $end_at)
    {
        $rows = Report_order::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id)
            ->select(DB::raw("date_format(concat(years,'-', months,'-', days), '%Y-%m-%d') as date,
                sum(order_count) as cnt, sum(`src_amount`) as src_amount,
                round(sum(`src_amount`)/sum(order_count),0) as avg_price,
                sum(cancel_amount) as cancel_amount, 
                sum(cancel_count) as cancel_count, 
                sum(discount_amount+coupon_discount+custom_discount) as discount_amount,
                sum(amount) as amount,
                sum(amount + cancel_amount) as invoice_amount"))
            ->groupBy("date")
            ->get();

        return $rows;
    }

    //載入日期銷售統計圖表資料
    public function load_chart_data()
    {
        if (!empty(request()->all()))
        {
            $start_at = request()->query('start_date', date("Y-m-d"));
            $end_at = request()->query('end_date', date("Y-m-d"));

            if ($start_at > $end_at)
            {
                response()->false;
            }

            $store_id = Session::get('store_id');

            //載入日期銷售統計資料
            $rows = $this->load_data($store_id, $start_at, $end_at);

            $dataSeries = array();
            $dataColumn = array();

            $data = array();
            $date_index = date("Y-m-d", strtotime($start_at));

            //建立查詢區間圖表輸出資料
            if (!empty($rows))
            {
                foreach ($rows as $row)
                {
                    //起始日期未達現有資料區間, 則填空值
                    while (strtotime($date_index) < strtotime($row->date))
                    {
                        $dataColumn[] = $date_index;
                        $data["src_amount"][] = (int) 0;
                        $data["avg_price"][] = (int) 0;
                        $data["discount_amount"][] = (int) 0;
                        $data["amount"][] = (int) 0;
                        $data["cancel_amount"][] = (int) 0;
                        $data["invoice_amount"][] = (int) 0;

                        $date_index = date("Y-m-d", strtotime($date_index." +1 day"));
                    }

                    //填入現有資料區間資料
                    $dataColumn[] = $row->date;
                    $data["src_amount"][] = (int) $row->src_amount;
                    $data["avg_price"][] = (int) $row->avg_price;
                    $data["discount_amount"][] = (int) $row->discount_amount;
                    $data["amount"][] = (int) $row->amount;
                    $data["cancel_amount"][] = (int) $row->cancel_amount;
                    $data["invoice_amount"][] = (int) $row->invoice_amount;

                    $date_index = date("Y-m-d", strtotime($row->date." +1 day"));
                }
            }

            //現有資料區間未達結束日期, 則填空值
            while (strtotime($date_index) <= strtotime(date("Y-m-d", strtotime($end_at))))
            {
                $dataColumn[] = $date_index;
                $data["src_amount"][] = (int) 0;
                $data["avg_price"][] = (int) 0;
                $data["discount_amount"][] = (int) 0;
                $data["amount"][] = (int) 0;
                $data["cancel_amount"][] = (int) 0;
                $data["invoice_amount"][] = (int) 0;

                $date_index = date("Y-m-d", strtotime($date_index." +1 day"));
            }

            if (!empty($data))
            {
                foreach ($data as $key => $val)
                {
                    $obj = new \StdClass();
                    switch ($key)
                    {
                        case 'src_amount':
                            $obj->name = '銷售總額';
                            $obj->data = $data["src_amount"];
                            break;
                        case 'avg_price':
                            $obj->name = '平均客單價';
                            $obj->data = $data["avg_price"];
                            break;
                        case 'discount_amount':
                            $obj->name = '折扣金額';
                            $obj->data = $data["discount_amount"];
                            break;
                        case 'amount':
                            $obj->name = '銷售淨額';
                            $obj->data = $data["amount"];
                            break;
                        case 'cancel_amount':
                            $obj->name = '作廢金額';
                            $obj->data = $data["cancel_amount"];
                            break;
                        case 'invoice_amount':
                            $obj->name = '發票開立金額';
                            $obj->data = $data["invoice_amount"];
                            break;
                    }

                    $dataSeries[] = $obj;
                }
            }

            return response()->json([
                'title' => '日期銷售統計',
                'sidetitle' => '金額',
                'bottomtitle' => '日期',
                'dataColumn' => $dataColumn,
                'dataSeries' => $dataSeries,
            ]);
        }
    }


    //加總日期銷售統計資料
    private function sum_data($store_id, $start_at, $end_at)
    {
        $rows = Report_order::where('start_hour', '>=', $start_at)
            ->where('end_hour', '<=', $end_at)
            ->where('store_id', $store_id)
            ->select(DB::raw("sum(order_count) as cnt, sum(`src_amount`) as src_amount,
                    round(sum(`src_amount`)/sum(order_count),0) as avg_price,
                    sum(cancel_amount) as cancel_amount, 
                    sum(cancel_count) as cancel_count, 
                    sum(discount_amount+coupon_discount+custom_discount) as discount_amount,
                    sum(amount) as amount,
                    sum(amount + cancel_amount) as invoice_amount"))
            ->get();

        return $rows;
    }
}
