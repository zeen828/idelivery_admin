<?php

namespace App\Admin\Controllers\Company;

use App\Model\idelivery\CampaignSetting;
use App\Models\idelivery\CouponSetting;

use App\Http\Controllers\Controller;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class CampaignController extends Controller
{
    use ModelForm;

    private $states = [
        'on'  => ['value' => 1, 'text' => '開啟', 'color' => 'success'],
        'off' => ['value' => 2, 'text' => '關閉', 'color' => 'danger'],
    ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content)
        {
            $content->header('活動設定');
            $content->description('註冊禮設定');

            $company_id = Session::get('company_id');
            $store_id   = 0;
            if ( ! empty(Session::get('store_id')))
            {
                $store_id = Session::get('store_id');
            }

            if(empty($company_id)) 
            {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $is_coupon_setting = CouponSetting::where('company_id', $company_id)->orWhere('store_id', $store_id)->get()->isEmpty();

            if($is_coupon_setting)
            {
                $box3 = new Box('提示', '請選擇先設定註冊禮優惠券');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $content->body($this->grid());
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
        return Admin::content(function (Content $content) use ($id)
        {
            $content->header('行銷活動設定');
            $content->description('編輯 註冊禮設定');

            $content->body($this->form()->edit($id));
        });
    }


    /**
     * Create interface.
     *
     * @return Content
     */
    public function create()
    {
        return Admin::content(function (Content $content) 
        {
            $content->header('活動設定');
            $content->description('建立 註冊禮設定');

            $content->body($this->form());
        });
    }


    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(CampaignSetting::class, function (Grid $grid) 
        {
            $company_id = Session::get('company_id');
            $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');

            $grid->model()->where(['company_id'=>$company_id, 'store_id'=>$store_id]);
            
            $grid->disableRowSelector();

            $grid->id('ID')->sortable();
            $grid->title('標題');
            $grid->start_date('開始時間');
            $grid->end_date('結束時間');
            $grid->status('是否啟用')->switch(function ($states) {
                return $states;
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
        return Admin::form(CampaignSetting::class, function (Form $form)
        {
            $company_id = Session::get('company_id');
            $form->hidden('company_id','公司編號')->default($company_id);

            $store_id = 0;

            if ( ! empty(Session::get('store_id')))
            {
                $store_id = Session::get('store_id');
            }

            $form->hidden('store_id','店家編號')->default($store_id);
            $form->hidden('campaign_condition_table')->default('campaign_condition_user_register');
            $form->hidden('campaign_offer_table')->default('campaign_offer_user_register');

            $form->text('title', '活動標題');
            $form->text('description', '活動描述');
            $form->switch('status', '活動發佈')->states($this->states);
            $form->datetimeRange('start_date', 'end_date', '開始結束時間');
            $form->select('offers.value', '選擇優惠券')->options(CouponSetting::where('company_id', $company_id)->orWhere('store_id', $store_id)->pluck('title', 'id'));
        });
    }
}