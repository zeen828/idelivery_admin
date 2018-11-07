<?php

namespace App\Admin\Controllers\Store\Lists;

use App\Models\idelivery\Order;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Table;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class OrdersController extends Controller
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

            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            if (empty($company_id)) {
                $content->row((new Box('提示', '請選擇所屬品牌!!'))->removable()->style('warning'));
                return false;
            }

            if (empty($store_id)) {
                $content->row((new Box('提示', '請選擇所屬店家!!'))->removable()->style('warning'));
                return false;
            }

            $content->header('訂單列表');
            $content->description('訂單列表');
            $content->row((new Box('篩選',' '))->removable()->collapsable()->style('info'));
            $content->row($this->grid());
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
        abort(404);
        exit;

        return Admin::content(function (Content $content) use ($id) {

            $content->header('header');
            $content->description('description');

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
        abort(404);
        exit;

        return Admin::content(function (Content $content) {
            $content->header('header');
            $content->description('description');

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
        return Admin::grid(Order::class, function (Grid $grid) {
            $grid->model()
                    ->where('company_id',Session::get('company_id'))
                    ->where('store_id',Session::get('store_id'));
            $grid->id('ID')->sortable();
            $grid->column('sn', 'SN');
            $grid->column('member_name', '姓名');
            $grid->column('purchase_phone', '購買電話');
            $grid->column('product_delivery', '取餐方法')->display(function ($pro_delivery){
                return ["1" => "外送", "2" => "外帶"][$pro_delivery];
            });
            $grid->column('amount', '訂單金額')->display(function ($amount){
                return number_format($amount);
            });
            $grid->column('status', '接單狀態')->display(function ($status){
                return [
                    1 => "訂單待確認",
                    2 => "餐點製作中",
                    3 => "等待外送",
                    4 => "等待外帶",
                    5 => "外送中",
                    6 => "處理完成",
                    7 => "無法接單",
                ][$status];
            });
            $grid->column('訂單明細')->expand(function () {
                $str = "<table class=\"table table-responsive table-hover\" style=\"margin-left:22%; width:78%\">
                        <thead>
                        <tr>
                            <th colspan=\"4\">訂單明細</th>
                        </tr>
                        <tr>
                            <th width=\"25%\">ID</th>
                            <th width=\"25%\">品項名稱</th>
                            <th width=\"25%\">價錢</th>
                            <th width=\"25%\">數量</th>
                        </tr>
                        </thead>
                        <tbody>
                        {ROWS}
                        </tbody>
                        </table>";
                $rows = [];
                foreach ($this->detail->toArray() as $detail){
                    $rows[] = sprintf("<tr>".
                                    "<td style='vertical-align: middle'>%s</td>".
                                    "<td style='vertical-align: middle'>%s</td>".
                                    "<td style='vertical-align: middle'>%s</td>".
                                    "<td style='vertical-align: middle'>%s</td>".
                                   "</tr>", $detail['id'], $detail['item_name'], $detail['item_price'], $detail['qty']);
                }
                return str_replace("{ROWS}", implode('', $rows), $str);
            }, '訂單明細');

            $grid->column('活動資訊')->expand(function(){
                $campaign_log = $this->campaign_log ;

                if (empty($campaign_log['id'])) return false;

                $str = "<table class=\"table table-responsive table-hover\" style=\"margin-left:22%; width:78%\">
                        <thead>
                        <tr>
                            <th colspan=\"4\">活動資料</th>
                        </tr>
                        <tr>
                            <th width=\"25%\">ID</th>
                            <th width=\"25%\">總金額</th>
                            <th width=\"25%\">結帳金額</th>
                            <th width=\"25%\">減免金額</th>
                        </tr>
                        </thead>
                        <tbody>
                        {ROWS}
                        </tbody>
                        </table>";
                $rows = [];
                $rows[] = sprintf("<tr>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "</tr>", $campaign_log['id'], $campaign_log['total_price'], $campaign_log['check_out_price'], $campaign_log['deduct_price']);
                return str_replace("{ROWS}", implode('', $rows), $str);
            });

            $grid->column('會員資料')->expand(function(){
                $member = $this->member;

                if (empty($member['uuid'])) return false;

                $str = "<table class=\"table table-responsive table-hover\" style=\"margin-left:22%; width:78%\">
                        <thead>
                        <tr>
                            <th colspan=\"4\">會員資料</th>
                        </tr>
                        <tr>
                            <th width=\"25%\">UUID</th>
                            <th width=\"25%\">姓名</th>
                            <th width=\"25%\">Email</th>
                            <th width=\"25%\">聯絡電話</th>
                        </tr>
                        </thead>
                        <tbody>
                        {ROWS}
                        </tbody>
                        </table>";
                $rows = [];
                $rows[] = sprintf("<tr>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "<td style='vertical-align: middle'>%s</td>".
                                "</tr>", $member['uuid'], $member['name'], $member['email'], $member['contact_phone']);

                return str_replace("{ROWS}", implode('', $rows), $str);
            },'會員資料');

            $grid->column('invoice.invoice_no', '發票號碼')->display(function($invoice_no){
                return (isset($invoice_no) === false) ? "尚未開發票" : $invoice_no;
            });
            $grid->column('create_time','下訂時間');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->disableIdFilter();
                $filter->like('id', 'Id');
                $filter->between('create_time','下訂時間')->datetime();
                $filter->like('sn', '訂單號碼');
                $filter->where(function ($query){
                    $query->whereHas('member', function($query){
                        $query->where('contact_phone', 'like', "%{$this->input}%") ;
                    })->orWhere('purchase_phone', 'like', "%{$this->input}%");
                }, '電話');

                // #重要1. 改用自訂view, 可完成直接顯示 filter    reference: http://discuss.laravel-admin.org/d/144-grid-modal/4
                // #重要2. 改用自訂view, 需要複製一份到 resources 相對路徑 reference: https://goo.gl/JTptoh
                app('view')->prependNamespace('admin', resource_path('views/admin'));
            });

            $grid->disableCreateButton();
            $grid->disableExport();
            $grid->disableActions();
            $grid->disableRowSelector();
        });
    }


    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Order::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}