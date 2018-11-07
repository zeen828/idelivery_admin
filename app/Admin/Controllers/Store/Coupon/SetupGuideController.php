<?php

namespace App\Admin\Controllers\Store\Coupon;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class SetupGuideController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('設定指南');
            $content->description('利用範例引導設定');

            $msg   = '範例';
            $rows  = [
                ["condition" => "滿100元", "offer" => "打9折", "url" => "store/set/coupon/order/amount/discount", "example" => "開幕慶結帳滿100元打9折"],
                ["condition" => "那些商品滿100元", "offer" => "打9折", "url" => "store/set/coupon/order/menu_group/amount/discount", "example" => "炸雞系列滿100元打9折"],
                ["condition" => "那些商品數量滿3杯", "offer" => "打8折", "url" => "store/set/coupon/order/menu_group/qty/discount", "example" => "氮氣茶系列滿3杯打8折"],
                ["condition" => "那些商品數量滿2杯", "offer" => "第2杯5折", "url" => "store/set/coupon/order/menu_group_n/qty/discount", "example" => "氮氣茶系列滿2杯第2杯5折"],
                ["condition" => "滿1000元", "offer" => "折扣100元", "url" => "store/set/coupon/order/amount/amount", "example" => "結帳滿1000元折扣100元"],
                ["condition" => "那些商品滿1000元", "offer" => "折扣100元", "url" => "store/set/coupon/order/menu_group/amount/amount", "example" => "星巴克咖啡系列滿1000元折扣100元"],
                ["condition" => "那些商品數量滿5杯", "offer" => "折扣50元", "url" => "store/set/coupon/order/menu_group/qty/amount", "example" => "氮氣茶系列滿5杯折扣50元"],
                ["condition" => "那些商品數量滿2件", "offer" => "第2件折扣10元", "url" => "store/set/coupon/order/menu_group_n/qty/amount", "example" => "可樂系列滿2件第2件折扣10元"],
                ["condition" => "4個便當", "offer" => "2個0元(免費)", "url" => "store/set/coupon/order/qty/qty", "example" => "4人同行(4個便當)2人免費"],
                ["condition" => "那些商品數量滿2件", "offer" => "第2件10元", "url" => "store/set/coupon/order/menu_group_n/qty/qty", "example" => "可樂系列滿2件第2件10元"],
            ];
            $style = 'success';

            $box = new Box($msg, view('campaign.guide', ['rows'=>$rows]));

            $content->body($box->style($style)->solid());

            // $content->body();

            // $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id)
    {
        return Admin::content(function (Content $content) use ($id) {

            $content->header('設定指南');
            $content->description('');

            // $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) {

            $content->header('設定指南');
            $content->description('');

            // $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        // return Admin::grid(Campaign_setting_form::class, function (Grid $grid) {
        // });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        // return Admin::form(Campaign_setting_form::class, function (Form $form) {
        // });
    }
}
