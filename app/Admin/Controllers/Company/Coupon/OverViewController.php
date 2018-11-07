<?php

namespace App\Admin\Controllers\Company\Coupon;

use App\Models\idelivery\Campaign_setting_form;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class OverViewController extends Controller
{
    use ModelForm;

    private $states = [
        'on'  => ['value' => 1, 'text' => '開啟', 'color' => 'primary'],
        'off' => ['value' => 0, 'text' => '關閉', 'color' => 'default'],
    ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('總覽');
            $content->description('');

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
        return Admin::content(function (Content $content) use ($id) {

            $content->header('總覽');
            $content->description('');

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
        return Admin::content(function (Content $content) {

            $content->header('總覽');
            $content->description('');

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
        return Admin::grid(Campaign_setting_form::class, function (Grid $grid) {

            $condition_match = ['Condition_amount' => '消費滿金額', 'Condition_qty' => '消費滿數量', 'Condition_amount_menu_group' => '消費那些商品滿金額', 'Condition_amount_menu_item' => '消費那些商品同品項滿金額', 'Condition_qty_menu_group' => '消費那些商品滿數量', 'Condition_qty_menu_item' => '消費那些商品同品項滿數量'];
            $offer_match = [
                'Offer_amount'            => '現金抵用',
                'Offer_amount_menu_group' => '現金抵用',
                'Offer_amount_menu_group_n' => '第幾件現金抵用',
                'Offer_amount_menu_item' => '同品項現金抵用',
                'Offer_amount_menu_item_n' => '同品項第幾件現金抵用',
                'Offer_discount' => '現金折扣(%)',
                'Offer_discount_menu_group' => '現金折扣(%)',
                'Offer_discount_menu_group_n' => '第幾件現金折扣(%)',
                'Offer_discount_menu_item' => '同品項現金折扣(%)',
                'Offer_discount_menu_item_n' => '同品項第幾件現金折扣(%)',
                'Offer_qty' => '最低價幾件變多少錢',
                'Offer_qty_menu_group' => '最低價幾件變多少錢',
                'Offer_qty_menu_group_n' => '最低價第幾件變多少錢',
                'Offer_qty_menu_item' => '同品項變多少錢',
                'Offer_qty_menu_item_n' => '同品項第幾件變多少錢',
                'Offer_coupon' => '優惠券',
                'Offer_points' => '紅利/點數',
            ];
            // 設定條件
            $company_id = Session::get('company_id');
            $store_id = 0;
            $grid->model()->where('types', 2)
                            ->where('company_id', $company_id)
                            ->where('store_id', $store_id)
                            ->orderBy('sort_by', 'desc');

            // 禁止功能
            $grid->disableCreation();//創建
            // $grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            $grid->disableActions();//操作

            // $grid->id('ID')->sortable();
            $grid->title(trans('campaign.field.title'));
            $grid->column('活動條件')->display(function() use ($condition_match) {
                if (isset($condition_match[$this->condition_table])) {
                    return $condition_match[$this->condition_table];
                } else {
                    return '無條件';
                }
                // $condition = '';
                // switch ($this->condition_table) {
                //     case 'Condition_amount':
                //         $condition = '消費滿金額';
                //         break;
                    
                //     case 'Condition_qty':
                //         $condition = '消費滿數量';
                //         break;

                //     case 'Condition_amount_menu_group':
                //         $condition = '消費那些商品滿金額';
                //         break;
                        
                //     case 'Condition_amount_menu_item':
                //         $condition = '消費那些商品同品項滿金額';
                //         break;

                //     case 'Condition_qty_menu_group':
                //         $condition = '消費那些商品滿數量';
                //         break;

                //     case 'Condition_qty_menu_item':
                //         $condition = '消費那些商品同品項滿數量';
                //         break;

                //     default:
                //         $condition = '無條件';
                //         break;
                // }

                // return $condition;
            });

            $grid->column('活動優惠')->display(function() use ($offer_match) {
                if (isset($offer_match[$this->offer_table])) {
                    return $offer_match[$this->offer_table];
                } else {
                    return '無優惠';
                }
                
                // $offer = '';
                // switch ($this->offer_table) {
                //     case 'Offer_amount':
                //     case 'Offer_amount_menu_group':
                //         $offer = '現金抵用';
                //         break;
                    
                //     case 'Offer_amount_menu_group_n':
                //         $offer = '第幾件現金抵用';
                //         break;

                //     case 'Offer_amount_menu_item':
                //         $offer = '同品項現金抵用';
                //         break;

                //     case 'Offer_amount_menu_item_n':
                //         $offer = '同品項第幾件現金抵用';
                //         break;

                //     case 'Offer_discount':
                //     case 'Offer_discount_menu_group':
                //         $offer = '現金折扣(%)';
                //         break;

                //     case 'Offer_discount_menu_group_n':
                //         $offer = '第幾件現金折扣(%)';
                //         break;

                //     case 'Offer_discount_menu_item':
                //         $offer = '同品項現金折扣(%)';
                //         break;

                //     case 'Offer_discount_menu_item_n':
                //         $offer = '同品項第幾件現金折扣';
                //         break;

                //     case 'Offer_qty':
                //     case 'Offer_qty_menu_group':
                //         $offer = '最低價幾件變多少錢';
                //         break;

                //     case 'Offer_qty_menu_group_n':
                //         $offer = '最低價第幾件變多少錢';
                //         break;

                //     case 'Offer_qty_menu_item':
                //         $offer = '同品項變多少錢';
                //         break;

                //     case 'Offer_qty_menu_item_n':
                //         $offer = '同品項第幾件變多少錢';
                //         break;

                //     case 'Offer_coupon':
                //         $offer = '優惠券';
                //         break;

                //     case 'Offer_points':
                //         $offer = '紅利/點數';
                //         break;

                //     default:
                //         $offer = '無優惠';
                //         break;
                // }

                // return $offer;
            });

            $grid->start_at(trans('campaign.field.start_at'));
            $grid->end_at(trans('campaign.field.end_at'));
            $grid->is_default(trans('campaign.field.default'))->switch($this->states);
            $grid->status(trans('campaign.field.status'))->switch($this->states);
            $grid->sort_by(trans('campaign.field.sort_by'))->orderable();
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

            $form->switch('status', trans('campaign.field.status'))->states($this->states);
            $form->switch('is_default', trans('campaign.field.default'))->states($this->states);
        });
    }
}
