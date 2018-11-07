<?php

namespace App\Admin\Controllers\Company;

use App\Lib\Geocoding;
use App\Model\idelivery\Promotion;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;
use Encore\Admin\Widgets\Box;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;

class PromotionController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => '1', 'text' => '啟用', 'color' => 'primary'),
        'off' => array('value' => '2', 'text' => '關閉', 'color' => 'default'),
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
            $content->header('活動設定');
            $content->description('促銷活動設定');

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
        return Admin::content(function (Content $content) use ($id) {
            $company_id = Session::get('company_id');

            $promotion = Promotion::find($id);
            if (empty($promotion) || empty($promotion->company_id)
                || $company_id != $promotion->company_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('促銷活動設定');
            $content->description('Promotions Setting');

            Session::put('promotion_id', $id);
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

            $content->header('促銷活動設定');
            $content->description('Promotions Setting');

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
        return Admin::grid(Promotion::class, function (Grid $grid) {
            $service_id = env('SERVICE_ID');
            $company_id = Session::get('company_id');
            $store_id = empty(Session::get('store_id')) ? 0 : Session::get('store_id');
            
            $grid->disableRowSelector();

            $grid->model()->where('service_id', $service_id)
                    ->where('company_id', $company_id)
                    ->where('store_id', $store_id);

            $grid->amount('滿金額')->style('text-align: right;width:10%');
            $grid->qty('達數量')->style('text-align: right;width:10%');
            $grid->point('獲得點數')->style('text-align: right;width:10%');
            $grid->column('expired', '有效期限')->display(function () {
                $str = str_replace('months', '個月', $this->expired);
                $str = str_replace('years', '年', $this->expired);
                $str = str_replace('year', '年', $this->expired);

                return '<span class="label label-success">'.$str.'</span>';
            })->style('width:10%');

            $grid->status('狀態')->switch($this->status_arr)->style('width:10%');

            $grid->column('start_date', '起始日期')->display(function () {
                $result = '';
                if (!empty($this->start_date)) {
                    $result = date("Y-m-d", strtotime($this->start_date));
                }

                return $result;
            })->style('width:15%');

            $grid->column('end_date', '結束日期')->display(function () {
                $result = '';
                if (!empty($this->end_date)) {
                    $result = date("Y-m-d", strtotime($this->end_date));
                }

                return $result;
            })->style('width:15%');            

            $grid->column('weekly', '週優惠日')->display(function () {
                switch ($this->weekly) {
                    case 0:
                        $week = '每週日';
                        break;
                    case 1:
                        $week = '每週一';
                        break;
                    case 2:
                        $week = '每週二';
                        break;
                    case 3:
                        $week = '每週三';
                        break;
                    case 4:
                        $week = '每週四';
                        break;
                    case 5:
                        $week = '每週五';
                        break;
                    case 6:
                        $week = '每週六';
                        break;
                    case 7:
                        $week = '';
                        break;
                }

                return '<span class="label label-primary">'.$week.'</span>';
            });

            $grid->column('daily', '倍數')->display(function () {
                return '<span class="label label-success">'.$this->daily.'倍</span>';
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
        return Admin::form(Promotion::class, function (Form $form) {

            $form->hidden('service_id','服務編號')->default(env('SERVICE_ID'));
            $form->hidden('company_id','公司編號')->default(Session::get('company_id'));
            $store_id = 0;
            if (!empty(Session::get('store_id'))) {
                $store_id = Session::get('store_id');
            }

            $form->hidden('store_id','店家編號')->default($store_id);
            $form->hidden('point_type_id','類別')->default(1);
            $form->hidden('id','促銷活動編號');

            $promotion = Promotion::where('service_id', env('SERVICE_ID'))
                                ->where('company_id', Session::get('company_id'))
                                ->where('store_id', $store_id)
                                ->select('*')
                                ->first();
            $setting = 1;
            $condition_value = 0;
            $promotion_point = 0;

            if (!empty($promotion)) {
                $promotion_point = $promotion->point;

                if ($promotion->amount == 0) {
                    $setting = 2;
                    $condition_value = $promotion->qty;
                } else {
                    $condition_value = $promotion->amount;
                }
            }

            $form->radio('qty', '基本設定')->options([1 => '滿金額', 2 => '達數量'])->default($setting); 
            $form->number('amount', '金額/數量')->rules('nullable|min:0')->default($condition_value);

            $form->number('point', '贈送點數')->rules('required|min:0')->default($promotion_point);
            $form->select('expired', '有效期限')->options(['+3 months' => '三個月', '+6 months' => '半年', 
                '+1 year' => '一年', '+18 months' => '一年半', '+2 years' => '二年', '+30 months' => '二年半',
                '+3 years' => '三年'])->rules('required');
            $form->switch('status', '狀態')->states($this->status_arr)->default(1);
            
            $form->divide();

            $form->datetime('start_date', '起始日期')->format('YYYY-MM-DD 00:00:00');
            $form->datetime('end_date', '結束日期')->format('YYYY-MM-DD 23:59:59');

            $form->select('weekly', '週優惠日')->options([0 => '星期日', 1 => '星期一', 2 => '星期二', 
                3 => '星期三', 4 => '星期四', 5 => '星期五', 6 => '星期六']);

            //因權限控管問題, 無法自訂 form, 故暫以欄位 daily為倍數送
            $form->number('daily', '倍數送')->rules('nullable');

            $form->saved(function (Form $form) {
                if ($form->qty == 2) {
                    Promotion::where('id', $form->model()->id)
                        ->update(['qty' => $form->amount, 'amount' => 0]);
                } else {
                    Promotion::where('id', $form->model()->id)
                        ->update(['amount' => $form->amount, 'qty' => 0]);
                }
            });

        });
     }
}
