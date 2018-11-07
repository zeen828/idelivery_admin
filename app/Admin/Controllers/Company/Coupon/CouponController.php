<?php

namespace App\Admin\Controllers\Company\Coupon;

use App\Models\idelivery\Coupon;
use App\Models\idelivery\Coupon_schedule_log;
use App\Models\system_member\Member_detail;
use App\Models\system_member\Member;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use App\Admin\Extensions\ExcelExpoter;

class CouponController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '啟用', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header('優惠券列表');
            $content->description(trans('idelivery.admin.index'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->grid());
            }
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
        Admin::js('/js/campaign_setting.js');
        return Admin::content(function (Content $content) use ($id) {
            $company_id = Session::get('company_id');
            $store_id = Session::get('store_id');
            $coupon_id = Session::put('coupon_id', $id);

            $campaign_setting = Campaign_setting_form::find($id);
            if (empty($campaign_setting) || empty($campaign_setting->company_id)
                || $company_id != $campaign_setting->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('優惠券列表');
            $content->description(trans('idelivery.admin.edit'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form()->edit($id));
            }
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        Admin::js('/js/campaign_setting.js');
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header('優惠券列表');
            $content->description(trans('idelivery.admin.create'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));
            } else {
                $content->body($this->form());
            }
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Coupon::class, function (Grid $grid) {

            $grid->disableCreateButton();
            $grid->disableActions();
            // $grid->actions(function ($actions) {
            //     $actions->disableDelete();
            //     $actions->disableEdit();
            // });

            $grid->exporter(new ExcelExpoter());

            $grid->id('編號')->sortable();
            $grid->sn('序號');
            $grid->column('member_detail_id', '發放否')->display(function ($member_detail_id) {
                if (!empty($member_detail_id)) {
                    return '<span class="label label-success">已發</span>';
                } else {
                    return '<span class="label label-default">未發</span>';
                }
            });

            $grid->column('status', '狀態')->switch($this->status_arr);

            $grid->column('發送')->display(function () {
                $result = '';
                if ($this->member_detail_id == 0) {
                    $result = '<a href="/admin/company/list/coupon/'. $this->id .'/edit"><i class="fa fa-send"></i></a>';
                }

                return $result;
            });

        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Member_detail::class, function (Form $form) {
            $form->text('member.account', '會員帳號')->default(0);
            $form->saving(function (Form $form) {
                $member = Member::where('account', $form->member['account'])->first();
                if (empty($member)) {
                    admin_toastr('查無此會員帳號','warning');
                    return false;
                }

                $member_detail = $form->model()->where('service_id', env('SERVICE_ID'))
                    ->where('company_id', Session::get('company_id'))
                    ->where('member_id', $member->id)
                    ->first();

                $form->model()->id = $member_detail->id;
            });

            $form->saved(function (Form $form) {
                $coupon = Coupon::find(Session::get('coupon_id'));
                $member = Member::where('account', $form->member['account'])->first();
                $member_detail = $form->model()->where('service_id', env('SERVICE_ID'))
                    ->where('company_id', Session::get('company_id'))
                    ->where('member_id', $member->id)
                    ->first();

                $coupon->member_detail_id = $member_detail->id;
                $coupon->save();

                $log = new Coupon_schedule_log();
                $result = $log->insert([
                    "member_detail_id" => $member_detail->id,
                    "setting_id" => $coupon->setting_id,
                    "coupon_id" => Session::get('coupon_id'),
                ]);

                if ($result === false) {
                    admin_toastr('優惠券發放失敗','warning');
                    return false;
                }
            });
        });
    }
}
