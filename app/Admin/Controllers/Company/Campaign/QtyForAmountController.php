<?php
// 滿X件現金抵用
namespace App\Admin\Controllers\Company\Campaign;

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

class QtyForAmountController extends Controller
{
    use ModelForm;

    private $states = [
        'on'  => ['value' => 1, 'text' => '開啟', 'color' => 'primary'],
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
            $content->header(trans('campaign.qty.amount.header'));
            $content->description(trans('campaign.index'));

            $company_id = Session::get('company_id');
            $event_id   = Session::get('event_id');

            if (empty($company_id) || empty($event_id)) {
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
            $content->header(trans('campaign.qty.amount.header'));
            $content->description(trans('campaign.edit'));

            $company_id = Session::get('company_id');
            $event_id   = Session::get('event_id');

            if (empty($company_id) || empty($event_id)) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                
                return false;
            }

            $campaign_setting = Campaign_setting_form::find($id);
            if (empty($campaign_setting) || empty($campaign_setting->company_id) || $company_id != $campaign_setting->company_id) {
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
            $content->header(trans('campaign.qty.amount.header'));
            $content->description(trans('campaign.create'));

            $company_id = Session::get('company_id');
            $event_id   = Session::get('event_id');

            if (empty($company_id) || empty($event_id)) {
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
            // $grid->disableCreateButton();
            // $grid->disablePagination();
            // $grid->disableFilter();
            // $grid->disableExport();
            // $grid->disableActions();

            $company_id = Session::get('company_id');
            $store_id   = 0;
            $event_id   = Session::get('event_id');

            $grid->model()
                ->where(['company_id'=>$company_id, 'store_id'=>$store_id])
                ->where(['event_id'=>$event_id, 'types'=>1])
                ->where('condition_table', 'Condition_qty')
                ->where('offer_table', 'Offer_amount');

            $grid->id(trans('campaign.field.id'))->sortable();
            $grid->title(trans('campaign.field.title'));
            $grid->description(trans('campaign.field.description'));
            $grid->condition_qty()->value(trans('campaign.qty.cond_value'));
            $grid->offer_amount()->value(trans('campaign.qty.amount.offer_value'));
            $grid->user_use_count(trans('campaign.field.user_use_count'));
            $grid->status(trans('campaign.field.status'))->switch($this->states);
            $grid->start_at(trans('campaign.field.start_at'));
            $grid->end_at(trans('campaign.field.end_at'));
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
            $company_id = Session::get('company_id');
            $store_id   = 0;
            $event_id   = Session::get('event_id');

            $form->hidden('company_id')->default($company_id);
            $form->hidden('store_id')->default($store_id);
            $form->hidden('event_id')->default($event_id);
            $form->hidden('condition_table')->default('Condition_qty');
            $form->hidden('offer_table')->default('Offer_amount');
            $form->hidden('types')->default(1);
            $form->hidden('repeat')->default(0);

            $form->tab(trans('campaign.setting_tab'), function($form) {
                $default_remark = "*營業時間依各店為主\n*全部門市皆可使用\n*本活動不得補差額，亦不得與店內其他行銷活動、優惠合併使用，逾期失效\n*xxx對本活動保留最終變更、解釋及終止之權利";

                $form->text('title', trans('campaign.field.title'))->rules('required');
                $form->text('description', trans('campaign.field.description'));
                $form->text('offer_max_value', trans('campaign.field.max_value'))
                    ->default(0)
                    ->rules('required|min:0',['regex' => trans('campaign.field.max_value').'必須是數字']);

                $form->textarea('remark', trans('campaign.field.remark'))->default($default_remark)->rules('required');
                $form->switch('hidden', trans('campaign.field.hidden'))->states($this->campaign_hidden)->default(0);
                $form->switch('status', trans('campaign.field.status'))->states($this->states)->default(1);
            })->tab(trans('campaign.time_tab'), function($form) {
                $datetime = new \DateTime;

                $form->datetimeRange('start_at', 'end_at', trans('campaign.field.duration'))
                        ->default(['start'=>$datetime->format('Y-m-01'), 'end'=>$datetime->modify('+1 months')->format('Y-m-01')]);
                $form->checkbox('week_days', '星期')->options(['1'=>'一', '2'=>'二', '3'=>'三', '4'=>'四', '5'=>'五', '6'=>'六', '0'=>'日']);
            })->tab(trans('campaign.cond_tab'), function($form) {
                $form->radio('product_delivery', trans('campaign.field.product_delivery'))
                    ->options([0=>'不限', 1=>'外送', 2=>'外帶', 3=>'內用'])->default(0);

                $form->text('condition_qty.value', trans('campaign.qty.cond_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.cond_value').'必須是數字'])
                    ->attribute('id', 'cond_value');
            })->tab(trans('campaign.offer_tab'), function($form) {
                $form->text('offer_amount.value', trans('campaign.qty.amount.offer_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.amount.offer_value').'必須是數字'])
                    ->attribute('id', 'offer_value');

                // $form->switch("plural", trans('campaign.field.plural'))->states($this->plural_switch);
            });

            $form->saved(function (Form $form) {
                $id = $form->model()->id;

                $form->model()->sort_by = $id;

                $form->model()->save();
            });

            Admin::script('qty_for_amount()');
        });
    }
}
