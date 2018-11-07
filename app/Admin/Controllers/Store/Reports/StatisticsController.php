<?php

namespace App\Admin\Controllers\Store\Reports;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;
use App\Model\idelivery\Orders;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Box;

class StatisticsController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');
            $content->header(trans('idelivery.statistics.title'));
            // $content->description(trans('idelivery.admin.index'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else if (empty($store_id)) {
                $box3 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body(view('admin.charts.store'));
            }
        });
    }

    public function loadYearOrders(Request $request)
    {
        $dataColumn = [];
        $dataDefault = [];
        $dataSeries = [];

        $this->getParameter($request, $storeId, $idxYear, $idxMonth, $idxDay);

        $field = "COUNT(*) AS total, DATE_FORMAT(create_time, '%c') AS month"
            . ($idxMonth ? ", DATE_FORMAT(create_time, '%e') AS day" : '')
            . ($idxDay ? ", DATE_FORMAT(create_time, '%k') AS hour" : '');;

        $db = DB::table('order')
            ->select(DB::raw($field))
            ->where('store_id', Session::get('store_id'))
            ->where(DB::raw('DATE_FORMAT(create_time, \'%Y\')'), $idxYear)
            ->groupBy("month")
            ->orderBy('month', 'asc');

        $this->parseParameter($db, $dataColumn, $dataDefault, $idxYear, $idxMonth, $idxDay);

        $rowsDetail = $db->get();

        foreach ($rowsDetail as $item) {
            if (!isset($dataSeries[$idxYear])) {
                $dataSeries[$idxYear] = [
                    'name' => $item->month .'月',
                    'data' => $dataDefault,
                ];
            }

            $flag = intval( ($idxDay) ? $item->hour : ($idxMonth ? $item->day : $item->month) ) - 1;
            $dataSeries[intval($idxYear)]['data'][$flag] += $item->total;
        }

        return $this->output($dataColumn, array_values($dataSeries), $idxYear, $idxMonth, $idxDay);
    }

    public function loadOrderItems(Request $request)
    {
        $dataColumn = [];
        $dataDefault = [];
        $dataSeries = [];

        $this->getParameter($request, $storeId, $idxYear, $idxMonth, $idxDay);

        $field = "item_id, SUM(qty) AS total, DATE_FORMAT(create_time, '%c') AS month"
            . ($idxMonth ? ", DATE_FORMAT(create_time, '%e') AS day" : '')
            . ($idxDay ? ", DATE_FORMAT(create_time, '%k') AS hour" : '');
        $db = DB::table('order_detail')
            ->select(DB::raw($field))
            ->whereRaw("order_id IN ( SELECT id FROM `order` WHERE store_id = $storeId )")
            ->where(DB::raw('DATE_FORMAT(create_time, \'%Y\')'), $idxYear)
            ->groupBy("item_id")
            ->groupBy("month")
            ->orderBy('item_id', 'asc');

        $this->parseParameter($db, $dataColumn, $dataDefault, $idxYear, $idxMonth, $idxDay);

        $rowsDetail = $db->get();

        foreach ($rowsDetail as $item) {
            if (!isset($dataSeries[intval($item->item_id)])) {
                $dataSeries[intval($item->item_id)] = [
                    'name' => $item->item_id,
                    'data' => $dataDefault,
                ];
            }

            $flag = intval( ($idxDay) ? $item->hour : ($idxMonth ? $item->day : $item->month) ) - 1;
            $dataSeries[intval($item->item_id)]['data'][$flag] += $item->total;
        }

        $rowsItem = DB::table('menu_item')
            ->select(DB::raw("id, name"))
            ->whereIn('id', array_keys($dataSeries))
            ->get();
        foreach ($rowsItem as $item) {
            $dataSeries[intval($item->id)]['name'] = $item->name;
        }

        return $this->output($dataColumn, array_values($dataSeries), $idxYear, $idxMonth, $idxDay);
    }

    private function getParameter($request, &$store, &$year, &$month, &$day)
    {
        $store = (int)Session::get('store_id');

        $year = $request->year && in_array($request->year, range(2017, date('Y'))) ? $request->year : date('Y');
        $month = $request->month && in_array($request->month, range(1, 12)) ? $request->month : 0;
        // $day = $request->day && in_array($request->day, range(1, 31)) ? $request->day: 0;

        // if ($month && $day) {
        //     $maxDay = date('t', strtotime(sprintf('%d-%02d', $year, $month)));
        //     $day = ($day <= $maxDay ? $day : $maxDay);
        // }
    }

    private function parseParameter(&$db, &$columnLabel, &$defaultValue, $year=0, $month=0, $day=0)
    {
        $maxColumn = 12;

        if ($month) {
            $maxColumn = date('t', strtotime(sprintf('%d-%02d', $year, $month)));
            $db = $db->where(DB::raw('DATE_FORMAT(create_time, \'%c\')'), $month)
                ->groupBy("day");
        }

        if ($day) {
            $maxColumn = 24;
            $db = $db->where(DB::raw('DATE_FORMAT(create_time, \'%e\')'), $day)
                ->groupBy("hour");
        }

        for ($idxColumn=1; $idxColumn<=$maxColumn; $idxColumn++) {
            array_push($columnLabel, $idxColumn . ($day ? '時' : ($month ? '日' : '月')));
            array_push($defaultValue, 0);
        }
    }

    private function output($columnLabel=[], $columnData=[], $year=0, $month=0, $day=0)
    {
        return response()->json([
            'title' => ($year .'年 ') . ($month ? ($month .'月 ') : '')  . ($day ? ($day .'日 ') : ''),
            'sidetitle' => '數量',
            'bottomtitle' => '月份數量',
            'dataColumn' => $columnLabel,
            'dataSeries' => array_values($columnData),
        ]);
    }
}
