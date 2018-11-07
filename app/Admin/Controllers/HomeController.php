<?php

namespace App\Admin\Controllers;

use App\Models\idelivery\Admin_notice;

use App\Model\idelivery\Admin_users;
use App\Model\idelivery\Member;
use App\Model\idelivery\Store;
use App\Model\idelivery\Orders;
use App\Model\idelivery\Company;
use App\Http\Controllers\Controller;

use Encore\Admin\Controllers\Dashboard;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Column;
use Encore\Admin\Layout\Content;
use Encore\Admin\Layout\Row;
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\InfoBox;// 表單用
use Encore\Admin\Widgets\Table;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class HomeController extends Controller
{
    public function index()
    {
        return Admin::content(function (Content $content) {
            $roles = Admin::user()->roles;

            $content->header('大後台');
            $content->description('Description...');

            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');

            $admin_notices = Admin_notice::get();
            if($admin_notices->isNotEmpty()) {
                //$headers = ['類型', '標題', '時間'];
                $headers = [];
                foreach ($admin_notices as $notice) {
                    $tmp = array();
                    switch ($notice->model) {
                        case 0:
                            $tmp['0'] = '一般訊息';
                            break;
                        case 1:
                            $tmp['0'] = '系統通知';
                            break;
                        case 2:
                            $tmp['0'] = '更新訊息';
                            break;
                        default:
                            $tmp['0'] = $notice->model;
                    }
                    $tmp['1'] = $notice->title;
                    $rows[] = $tmp;
                }
                $table = new Table(array(), $rows);
                $box = new Box('系統公告', $table->render());
                $content->row($box);
            }
        });
    }

    /**
     * 切換店家功能
     */
    public function change_config_id(Request $request)
    {
        if (Admin::user()->can('system.change')) {
            $get_str = $request->get('store_id');
            $get_arr = explode("-", $get_str);
            $company_id = (isset($get_arr['0']))? $get_arr['0'] : 0;
            $store_id = (isset($get_arr['1']))? $get_arr['1'] : 0;
            if(!empty($company_id)){
                Session::put('company_id', (int)$company_id);
            }
            if(!empty($store_id)){
                Session::put('store_id', (int)$store_id);
            }
            unset($store_id);
            unset($company_id);
            unset($get_arr);
            unset($get_str);
        }
        //echo url()->current(); echo url()->full(); echo url()->previous();
        //var_dump(Session::all());
        return redirect(url()->previous());
    }


    public function management_info()
    {
        return Admin::content(function (Content $content) {
            $content->header('大後台');
            $content->description('Description...');
            $content->row(function ($row) {
                $row->column(3, new InfoBox('上架分店數', 'users', 'aqua', '/demo/users', '0'));
                $row->column(3, new InfoBox('VIP分店數', 'shopping-cart', 'green', '/demo/orders', '0'));
                $row->column(3, new InfoBox('會員數', 'book', 'yellow', '/demo/articles', '0'));
                $row->column(3, new InfoBox('App下載數', 'file', 'red', '/demo/files', '698726'));
            });
            $content->row(function ($row) {
                $row->column(4, new InfoBox('本月線上營業額', 'users', 'aqua', '/demo/users', '0'));
                $row->column(4, new InfoBox('上月線上營業額', 'shopping-cart', 'green', '/demo/orders', '0'));
                $row->column(4, new InfoBox('簡訊剩餘點數', 'file', 'red', '/demo/files', '698726'));
            });
        });
    }


    public function company_info()
    {
        return Admin::content(function (Content $content) {
            $content->header('總部');
            $content->description('Description...');

            $company_id = Session::get('company_id');

            $content->row(function ($row) use ($company_id) {
                $row->column(3, new InfoBox('上架分店數', 'users', 'aqua', '#', Company::getStoreCount($company_id)));
                $row->column(3, new InfoBox('VIP分店數', 'shopping-cart', 'green', '#', Company::getVipStoreCount($company_id)));
                $row->column(3, new InfoBox('會員數', 'book', 'yellow', '#', Member::getCount($company_id)));
                $row->column(3, new InfoBox('App下載數', 'file', 'red', '#', '698726'));
            });

            $content->row(function ($row) use ($company_id) {
                $row->column(4, new InfoBox('本月線上營業額', 'users', 'aqua', '#', Orders::getThisMonthIncome($company_id)));
                $row->column(4, new InfoBox('上月線上營業額', 'shopping-cart', 'green', '#', Orders::getLastMonthIncome($company_id)));
                $row->column(4, new InfoBox('簡訊剩餘點數', 'file', 'red', '#', '698726'));
            });
        });
    }


    public function store_info()
    {
        return Admin::content(function (Content $content) {
            $content->header('店家');
            $content->description('Description...');

            $store_id = Session::get('store_id');

            $content->row(function ($row) use ($store_id) {
                $row->column(6, new InfoBox('本日訂單數', 'users', 'aqua', '#', Orders::getStoreTodayOrders($store_id)));
                $row->column(6, new InfoBox('本日訂購金額', 'shopping-cart', 'green', '#', Orders::getStoreTodayOrdersAmount($store_id)));
            });

            $content->row(function ($row) use ($store_id) {
                $row->column(6, new InfoBox('本月訂單數', 'users', 'aqua', '#', Orders::getStoreMonthlyOrders($store_id)));
                $row->column(6, new InfoBox('本月訂購金額', 'shopping-cart', 'green', '#', Orders::getStoreMonthlyOrdersAmount($store_id)));
            });
        });
    }
}
