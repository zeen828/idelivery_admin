<?php
// 那些商品滿多少數量-那些商品最低價第幾件變多少錢
namespace App\Admin\Controllers\Company\Coupon\MenuGroup;

use App\Models\idelivery\Campaign_setting_form;
use App\Models\idelivery\Menu_item;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;

class QtyForNQtyController extends Controller
{
    use ModelForm;

    private $status_switch = array(
        'on'  => array('value' => 1, 'text' => '啟用', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    private $repeat_switch = array(
        'on'  => array('value' => 1, 'text' => '累計', 'color' => 'primary'),
        'off' => array('value' => 0, 'text' => '單次', 'color' => 'default'),
    );

    // private $plural_arr = array(
    //     'on'  => array('value' => 1, 'text' => '合併', 'color' => 'primary'),
    //     'off' => array('value' => 0, 'text' => '單次', 'color' => 'default'),
    // );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header(trans('campaign.qty.qty_item_group_n.header'));
            $content->description(trans('campaign.index'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
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
            $company_id = Session::get('company_id');
            //$store_id = Session::get('store_id');

            $campaign_setting = Campaign_setting_form::find($id);
            if (empty($campaign_setting) || empty($campaign_setting->company_id)
                || $company_id != $campaign_setting->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header(trans('campaign.qty.qty_item_group_n.header'));
            $content->description(trans('campaign.edit'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $box = new Box('票圖預覽', view('admin/coupon'));
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
            $company_id = Session::get('company_id');
            $content->header(trans('campaign.qty.qty_item_group_n.header'));
            $content->description(trans('campaign.create'));
            if (empty($company_id)) {
                $box3 = new Box('提示', '請選擇所屬品牌!!');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            $box = new Box('票圖預覽', view('admin/coupon'));
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
            $company_id = Session::get('company_id');
            $store_id   = 0;

            $grid->model()->where('types', 2)
                ->where('company_id', '=', $company_id)
                ->where('store_id', '=', $store_id)
                ->where('condition_table', '=', 'Condition_qty_menu_group')
                ->where('offer_table', '=', 'Offer_qty_menu_group_n');

            $grid->id(trans('coupon.field.id'))->sortable();
            $grid->title(trans('coupon.field.title'));
            $grid->description(trans('coupon.field.description'));
            $grid->column(trans('campaign.qty.menu_item'))->display(function() {
                $menu_item = Menu_item::whereIn('id', $this->condition_qty_menu_group->menu_item_ids)->get();
                
                $result = array();
                foreach ($menu_item as $value) {
                    $result[] = $value->name;
                }

                return implode(', ', $result);
            });

            $grid->offer_qty_menu_group_n()->value(trans('campaign.qty.qty_item_group_n.offer_value'));
            $grid->offer_qty_menu_group_n()->n_th(trans('campaign.qty.qty_item_group_n.nth'));
            $grid->offer_qty_menu_group_n()->price(trans('campaign.qty.qty_item_group_n.price'));
            $grid->used_count(trans('coupon.field.used_count'));
            $grid->status(trans('coupon.field.status'))->switch($this->status_switch);
            $grid->start_at(trans('coupon.field.start_at'));
            $grid->end_at(trans('coupon.field.end_at'));

            $grid->actions(function ($actions) {
                $locks = $actions->row->locks;
                if ($locks == 1) {
                    $actions->disableDelete();
                    $actions->disableEdit();
                }
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
        return Admin::form(Campaign_setting_form::class, function (Form $form) {

            $company_id = Session::get('company_id');
            $store_id   = 0;

            $form->hidden('company_id')->default($company_id);
            $form->hidden('store_id')->default($store_id);
            $form->hidden('condition_table')->default('Condition_qty_menu_group');
            $form->hidden('offer_table')->default('Offer_qty_menu_group_n');
            $form->hidden('types')->default(2);
            $form->hidden('sn_gen')->default(1);

            $form->tab(trans('coupon.setting_tab'), function($form) {
                $remark_txt = "*營業時間依各店為主\n*全部門市皆可使用\n*本券不得補差額，亦不得與店內其他行銷活動、優惠合併使用，逾期失效\n*xxx對本活動保留最終變更、解釋及終止之權利";
                $form->text("title", trans('coupon.field.title'))->rules('required');
                $form->text("description", trans('coupon.field.description'));
                $form->text("max_qty", trans('coupon.field.max_qty'))
                    ->default(0)
                    ->rules('min:0')
                    ->attribute('style', 'width:150px;');
                $form->text("user_use_count", trans('coupon.field.user_use_count'))
                    ->default(1)
                    ->rules('required|min:0')
                    ->attribute('style', 'width:150px;');
                $form->textarea("remark", trans('coupon.field.remark'))
                    ->rows(5)
                    ->default($remark_txt);
                $form->switch('status', trans('coupon.field.status'))->states($this->status_switch)->default(1);
            })->tab(trans('coupon.time_tab'), function ($form) {
                $datetime = new \DateTime;

                $form->radio("kind", "類型")->options([1 => '時間區間', 2 => '領後幾日'])
                    ->default(1);
                $form->datetimeRange('start_at', 'end_at', trans('coupon.field.duration'))
                    ->default(['start'=>$datetime->format('Y-m-01 00:00:00'), 'end'=>$datetime->modify('+1 months')->format('Y-m-01 00:00:00')]);
                $form->text("kind_value", trans('coupon.field.after_day'));
                //$form->checkbox('week_days', '星期')->options(['1'=>'一', '2'=>'二', '3'=>'三', '4'=>'四', '5'=>'五', '6'=>'六', '0'=>'日']);

            })->tab(trans('coupon.cond_tab'), function ($form) use ($company_id, $store_id) {
                $form->radio("product_delivery", trans('coupon.field.product_delivery'))
                    ->options([0 => '不限', 1 => '外送', 2 => '外帶', 3 => '內用'])
                    ->default(0);

                $form->checkbox('condition_qty_menu_group.menu_item_ids', trans('campaign.qty.menu_item'))
                    ->options(Menu_item::where(['company_id' => $company_id, 'store_id' => $store_id])->pluck('name', 'id'))
                    ->rules('required');

                $form->text('condition_qty_menu_group.value', trans('campaign.qty.cond_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.cond_value').'必須是數字'])
                    ->attribute('id', 'cond_value');
            })->tab(trans('coupon.offer_tab'), function ($form) {
                // 第幾件有優惠
                $form->text('offer_qty_menu_group_n.n_th', trans('campaign.qty.qty_item_group_n.nth'))
                    ->default(0)
                    ->help('商品價錢由低到高排序')
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.qty_item_group_n.nth').'必須是數字'])
                    ->attribute('id', 'offer_value');
                // 預設一件
                $form->hidden('offer_qty_menu_group_n.value')->default(1);
                // 優惠多少錢
                $form->text('offer_qty_menu_group_n.price', trans('campaign.qty.qty_item_group_n.price'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.qty_item_group_n.price').'必須是數字'])
                    ->attribute('id', 'offer_value');

                $form->divide();
                $form->switch("repeat", trans('campaign.field.repeat'))->states($this->repeat_switch);
                // 最多優惠幾件
                $form->text('offer_qty_menu_group_n.max_value', trans('campaign.qty.qty.offer_max_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.qty.offer_max_value').'必須是數字']);

                // $form->switch("plural", trans('campaign.field.plural'))->states($this->plural_switch);
            });

            $form->saving(function ($form) {
                if ($form->kind == 2) {
                    $form->start_at = date("Y-m-d H:i:s");
                    $form->end_at = date("Y-m-d H:i:s", strtotime($form->start_at . "+" . $form->kind_value . " days"));
                    $form->sn_gen = 2;
                }
            });

            $form->saved(function (Form $form) {
                $id = $form->model()->id;
                $form->model()->sort_by = $id;

                $campaign_setting = Campaign_setting_form::find($id);
                $campaign_setting->offer_qty_menu_group_n->menu_item_ids = $campaign_setting->condition_qty_menu_group->menu_item_ids;
                $campaign_setting->offer_qty_menu_group_n->save();

                $form->model()->save();
            });

            Admin::script('qty()');
        });
    }
}