<?php

/**
 * Laravel-admin - admin builder based on Laravel.
 * @author z-song <https://github.com/z-song>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 * Encore\Admin\Form::forget(['map', 'editor']);
 *
 * Or extend custom form field:
 * Encore\Admin\Form::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */

Admin::js('/js/highcharts.min.js');

Encore\Admin\Form::forget(['map', 'editor']);

use Encore\Admin\Form;
use App\Admin\Extensions\Summernote;

Form::extend('summernote', Summernote::class);

use App\Model\idelivery\Admin_users;

use Encore\Admin\Facades\Admin;

use Illuminate\Support\Facades\Session;

use App\Admin\Extensions\Column\ExpandRow;
use Encore\Admin\Grid\Column;

Column::extend('expand', ExpandRow::class);
Form::extend('coupon', CouponFormat::class);
Form::extend('campaign', CampaignFormat::class);

Admin::navbar(function (\Encore\Admin\Widgets\Navbar $navbar) {
    // 有沒有登入
    if(Admin::user()){
        // 判斷權限
        if (Admin::user()->can('system.change')) {
            $login_id = Admin::user()->id;
            $user = Admin_users::find($login_id);
            $store_id = Session::get('store_id');
            $option['store_change'] = [];
            foreach ($user->store as $store) {
                //var_dump($store->company);
                $selected = ($store_id == $store->id)? ' selected' : '';
                $option['store_change'][] = [
                    'id' => sprintf("%s-%s", $store->company->id, $store->id),
                    //'id' => $store->id,
                    'title' => sprintf("&lt;%s&gt;-(%d)%s", $store->company->name, $store->id, $store->name),
                    //'title' => $store->name,
                    'selected' => $selected,
                ];
            }
            $navbar->left(view('admin.navbar.store_change', $option));
        }
    }
});