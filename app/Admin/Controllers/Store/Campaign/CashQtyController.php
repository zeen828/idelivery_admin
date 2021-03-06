<?php

namespace App\Admin\Controllers\Store\Campaign;

use App\Models\idelivery\Campaign_setting_form;
use App\Models\idelivery\Campaign_event;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class CashQtyController extends Controller
{
    use ModelForm;

    private $status_arr = [
        'on'  => ['value' => 1, 'text' => '啟用', 'color' => 'primary'],
        'off' => ['value' => 2, 'text' => '關閉', 'color' => 'default'],
    ];

    private $campaign_hidden = [
        'on'  => ['value' => 0, 'text' => '顯示', 'color' => 'primary'],
        'off' => ['value' => 1, 'text' => '隱藏', 'color' => 'default']
    ];

    // private $plural_switch = [
    //     'on'  => ['value' => 1, 'text' => '合併', 'color' => 'primary'],
    //     'off' => ['value' => 0, 'text' => '單次', 'color' => 'default'],
    // ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        Session::forget('event_id');
        $event = Campaign_event::where('keyword', $request->segment(5))->first();
        Session::put('event_id', $event->id);

        return Admin::content(function (Content $content) {
            $content->header(trans('coupon.cash.qty.header'));
            $content->description(trans('coupon.index'));
            
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');
            $event_id   = Session::get('event_id');

            if(empty($company_id) || empty($event_id) || empty($store_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
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
        Admin::js('/js/campaign_setting.js');
        return Admin::content(function (Content $content) use ($id) {
            $content->header(trans('coupon.cash.qty.header'));
            $content->description(trans('coupon.edit'));
            
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');
            $event_id   = Session::get('event_id');

            if(empty($company_id) || empty($event_id) || empty($store_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $setting = Campaign_setting_form::find($id);

            if (empty($setting) || empty($setting->store_id) || $store_id != $setting->store_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $box = new Box('票圖預覽', view('admin/campaign'));
            $content->row($box->removable()->style('info'));
            $content->row($this->form()->edit($id));
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
            $content->header(trans('coupon.cash.qty.header'));
            $content->description(trans('coupon.create'));
            
            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');
            $event_id   = Session::get('event_id');

            if(empty($company_id) || empty($event_id) || empty($store_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $box = new Box('票圖預覽', view('admin/campaign'));
            $content->row($box->removable()->style('info'));
            $content->row($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Campaign_setting_form::class, function (Grid $grid) {
            $grid->disableRowSelector();

            $grid->model()
                ->where('types', 1)
                ->where('company_id', '=', Session::get('company_id'))
                ->where('store_id', '=', Session::get('store_id'))
                ->whereNull('condition_table')
                ->where('offer_table', '=', 'Offer_qty');

            $grid->id(trans('coupon.field.id'))->sortable();
            $grid->title(trans('coupon.field.title'));
            $grid->description(trans('coupon.field.description'));
            $grid->offer_qty()->value(trans('coupon.cash.qty.offer_value'));
            $grid->used_count(trans('coupon.field.used_count'));
            $grid->status(trans('coupon.field.status'))->switch($this->status_arr);
            $grid->start_at(trans('coupon.field.start_at'));
            $grid->end_at(trans('coupon.field.end_at'));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Campaign_setting_form::class, function (Form $form) {
            $form->hidden('company_id', '品牌編號')->default(Session::get('company_id'));
            $form->hidden('store_id')->default(Session::get('store_id'));
            $form->hidden('event_id')->default(Session::get('event_id'));
            $form->hidden('offer_table')->default('Offer_qty');
            $form->hidden('types')->default(1);

            $form->tab(trans('coupon.setting_tab'), function($form) {
                $remark_txt = "*營業時間依各店為主\n*全部門市皆可使用\n*本券不得補差額，亦不得與店內其他行銷活動、優惠合併使用，逾期失效\n*xxx對本活動保留最終變更、解釋及終止之權利";
                
                $form->text("title", trans('coupon.field.title'))->rules('required');
                $form->text("description", trans('coupon.field.description'));
                $form->textarea("remark", trans('coupon.field.remark'))->rows(5)->default($remark_txt);
                $form->switch('hidden', trans('campaign.field.hidden'))->states($this->campaign_hidden)->default(0);
                $form->switch('status', trans('coupon.field.status'))->states($this->status_arr)->default(1);
            })->tab(trans('coupon.time_tab'), function ($form) {
                $datetime = new \DateTime;

                $form->datetimeRange('start_at', 'end_at', trans('campaign.field.duration'))
                        ->default(['start'=>$datetime->format('Y-m-01'), 'end'=>$datetime->modify('+1 months')->format('Y-m-01')]);
                $form->checkbox('week_days', '星期')->options(['1'=>'一', '2'=>'二', '3'=>'三', '4'=>'四', '5'=>'五', '6'=>'六', '0'=>'日']);
            })->tab(trans('coupon.cond_tab'), function ($form) {
                $form->radio("product_delivery", trans('coupon.field.product_delivery'))
                    ->options([0 => '不限', 1 => '外送', 2 => '外帶', 3 => '內用'])
                    ->default(0);
            })->tab(trans('coupon.offer_tab'), function ($form) {
                $form->text("offer_qty.value", trans('coupon.cash.qty.offer_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('coupon.cash.qty.offer_value').'必須是數字'])
                    ->attribute('id', 'offer_value')
                    ->attribute('style', 'width:150px;');
                $form->text("offer_qty.max_value", trans('coupon.field.offer_max_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('coupon.field.offer_max_value').'必須是數字'])
                    ->attribute('id', 'offer_max_value')
                    ->attribute('style', 'width:150px;');

                // $form->switch("plural", trans('campaign.field.plural'))->states($this->plural_switch);
            });

            $form->saved(function (Form $form) {
                $id = $form->model()->id;

                $form->model()->sort_by = $id;

                $form->model()->save();
            });

            Admin::script('amount_for_qty(false)');
        });
    }
}