<?php
// 那些商品滿多少數量-指定商品第幾件金額打折
namespace App\Admin\Controllers\Company\Campaign\MenuItem;

use App\Models\idelivery\Campaign_setting_form;
use App\Models\idelivery\Menu_item;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class QtyForNDiscountController extends Controller
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

    private $repeat_switch = [
        'on'  => ['value' => 1, 'text' => '累計', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => '單次', 'color' => 'default'],
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
        return Admin::content(function (Content $content) {
            $content->header('指定商品同品項第Ｎ件打Ｍ折');
            $content->description(trans('campaign.index'));

            $company_id = Session::get('company_id');

            if (empty($company_id)) {
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
        // Admin::js('/js/campaign_setting.js');
        return Admin::content(function (Content $content) use ($id) {
            $content->header('指定商品同品項第Ｎ件打Ｍ折');
            $content->description(trans('campaign.edit'));

            $company_id = Session::get('company_id');

            if (empty($company_id)) {
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

            // $box = new Box('票圖預覽', view('admin/campaign'));
            // $content->row($box->removable()->style('info'));
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
        // Admin::js('/js/campaign_setting.js');
        return Admin::content(function (Content $content) {
            $content->header('指定商品同品項第Ｎ件打Ｍ折');
            $content->description(trans('campaign.create'));

            $company_id = Session::get('company_id');

            if(empty($company_id)) {
                $box3 = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($box3->removable()->style('warning'));

                return false;
            }

            // $box = new Box('票圖預覽', view('admin/campaign'));
            // $content->row($box->removable()->style('info'));
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

            $grid->model()
                ->where(['company_id'=>$company_id, 'store_id'=>$store_id])
                ->where('types', 2)
                ->where('condition_table', 'Condition_qty_menu_item')
                ->where('offer_table', 'Offer_discount_menu_item_n');

            $grid->id(trans('campaign.field.id'))->sortable();
            $grid->title(trans('campaign.field.title'));
            $grid->description(trans('campaign.field.description'));
            $grid->column(trans('campaign.qty.menu_item'))->display(function() {
                if (count($this->condition_qty_menu_item)) {
                    $menu_item = Menu_item::whereIn('id', $this->condition_qty_menu_item->menu_item_ids)->get();
                
                    $result = array();
                    foreach ($menu_item as $value) {
                        $result[] = $value->name;
                    }
    
                    return implode(', ', $result);
                }
            });

            $grid->condition_qty_menu_item()->value('滿幾件');
            $grid->offer_discount_menu_item_n()->n_th('第幾件');
            $grid->column(trans('campaign.qty.discount_item_group_n.offer_value'))->display(function() {
                return (int) $this->Offer_discount_menu_item_n->value.' %';
            });

            $grid->used_count(trans('campaign.field.user_use_count'));
            $grid->status(trans('campaign.field.status'))->switch($this->states);
            $grid->start_at(trans('campaign.field.start_at'));
            $grid->end_at(trans('campaign.field.end_at'));

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
            $form->hidden('condition_table')->default('Condition_qty_menu_item');
            $form->hidden('offer_table')->default('Offer_discount_menu_item_n');
            $form->hidden('types')->default(2);
            $form->hidden('sn_gen')->default(1);

            $form->tab(trans('campaign.setting_tab'), function($form) {
                $remark_txt = "*營業時間依各店為主\n*全部門市皆可使用\n*本券不得補差額，亦不得與店內其他行銷活動、優惠合併使用，逾期失效\n*xxx對本活動保留最終變更、解釋及終止之權利";
                $form->text("title", trans('coupon.field.title'))->rules('required');
                $form->text("description", trans('coupon.field.description'));
                $form->text("offer_max_value", trans('coupon.field.max_value'))
                    ->default(0)
                    ->rules('required|min:0')
                    ->attribute('style', 'width:150px;');
                $form->text("max_qty", trans('coupon.field.max_qty'))
                    ->default(0)
                    ->rules('min:0')
                    ->attribute('style', 'width:150px;');
                $form->text("user_use_count", trans('coupon.field.user_use_count'))
                    ->default(1)
                    ->rules('required|min:0')
                    ->attribute('style', 'width:150px;');
                $form->textarea("remark", trans('coupon.field.remark'))->rows(5)->default($remark_txt);
                $form->switch('status', trans('coupon.field.status'))->states($this->status_switch)->default(1);
            })->tab(trans('campaign.time_tab'), function($form) {
                $datetime = new \DateTime;

                $form->radio("kind", "類型")->options([1 => '時間區間', 2 => '領後幾日'])
                    ->default(1);
                $form->datetimeRange('start_at', 'end_at', trans('coupon.field.duration'))
                    ->default(['start'=>$datetime->format('Y-m-01 00:00:00'), 'end'=>$datetime->modify('+1 months')->format('Y-m-01 00:00:00')]);
                $form->text("kind_value", trans('coupon.field.after_day'));
            })->tab(trans('campaign.cond_tab'), function($form) use ($company_id, $store_id) {
                $form->radio('product_delivery', trans('campaign.field.product_delivery'))
                    ->options([0=>'不限', 1=>'外送', 2=>'外帶', 3=>'內用'])->default(0);

                $form->checkbox('condition_qty_menu_item.menu_item_ids', trans('campaign.qty.menu_item'))
                    ->options(Menu_item::where(['company_id' => $company_id, 'store_id' => $store_id])->pluck('name', 'id'))
                    ->rules('required');

                $form->text('condition_qty_menu_item.value', trans('campaign.qty.cond_value'))
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.cond_value').'必須是數字'])
                    ->attribute('id', 'cond_value');
            })->tab(trans('campaign.offer_tab'), function($form) {
                // 第幾件有優惠
                $form->text('offer_discount_menu_item_n.n_th', '第幾件')
                    ->default(1)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => '[第幾件]必須是數字'])
                    ->attribute('id', 'offer_value');
                // 優惠多少折數
                $form->rate('offer_discount_menu_item_n.value', '折數')
                    ->default(0)
                    ->rules('required|regex:/^\d+(\.\d{1})?\d{0,}$/',['regex' => trans('campaign.qty.discount_item_group_n.offer_value').'必須是數字'])
                    ->attribute('id', 'offer_value');

                // $form->switch("plural", trans('campaign.field.plural'))->states($this->plural_switch);
                $form->divide();
                $form->switch("repeat", trans('campaign.field.repeat'))->states($this->repeat_switch);
            });

            $form->saved(function (Form $form) {
                if ($form->kind == 2) {
                    $form->start_at = date("Y-m-d H:i:s");
                    $form->end_at = date("Y-m-d H:i:s", strtotime($form->start_at . "+" . $form->kind_value . " days"));
                    $form->sn_gen = 2;
                }
            });

            // Admin::script('qty()');
        });
    }
}