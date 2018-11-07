<?php

namespace App\Admin\Controllers\Company\Exchanges;

use App\Models\idelivery\Exchanges;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class ExchangesController extends Controller
{
    use ModelForm;

    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '上架', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '下架', 'color' => 'default'),
    );

    /**
     * Guide Page (Custom Add)
     *
     * @return Content
     */

    public function guide()
    {
        return Admin::content(function (Content $content) {

            $content->header('設定指南');
            $content->description('利用範例引導設定');

            $msg   = '範例';
            $rows  = [
                ["condition" => "實體商品", "offer" => "-", "url" => route('company.exchanges.item.index'), "example" => "使用100點換泰迪熊"],
                ["condition" => "優惠券", "offer" => "-", "url" => route('company.exchanges.coupon.index'), "example" => "使用100點換9折折價券"],
            ];
            $style = 'success';

            $box = new Box($msg, view('exchange.guide', ['rows'=>$rows]));

            $content->body($box->style($style)->solid());
        });
    }

    /**
     * Overview Page (Custom Add)
     *
     * @return Content
     */
    public function overview()
    {
        return Admin::content(function (Content $content) {

            $content->header('總覽');
            $content->description('');

            $content->body($this->grid('overview'));
        });
    }

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        abort(404);
        exit;
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
    protected function grid($type)
    {
        return Admin::grid(Exchanges::class, function (Grid $grid) use ($type) {

            $company_id = Session::get('company_id');
            $store_id   = 0;

            $grid->model()
                ->where('company_id', $company_id)
                ->where('store_id', $store_id);

            $grid->id('ID')->sortable();
            $grid->column('name', '兌換品名稱')->style('width:20%');
            $grid->column('image', '兌換品圖片')->image(env('ADMIN_UPLOAD_URL', ''), 50)->style('width:5%');
            $grid->column('description', '兌換品說明')->style('width:25%');
            $grid->column('point', '點數')->style('text-align: right;width:5%');
            $grid->column('stock', '可兌換量')->style('text-align: right;width:5%');

            // date format
            $cb = function ($date) {return date("Y-m-d", strtotime($date));};
            $grid->column('start_date', '起始日期')->display($cb)->style('width:10%');
            $grid->column('end_date',   '結束日期')->display($cb)->style('width:10%');

            $grid->column('status', '上架否')->switch($this->status_arr)->style('width:5%');
            $grid->column('exchanges_type', '兌換類型')->display(function($type){ return ["1" => "實體商品", "2" => "優惠券"][$type];});
            $grid->column('優惠活動')->expand(function(){
                $campaign_setting = $this->campaign_setting;
                if (isset($campaign_setting['id']) === false) return false;

                $str = "<table class=\"table table-responsive table-hover\" style=\"margin-left:22%; width:78%\">
                        <thead>
                        <tr>
                            <th colspan=\"4\">優惠活動資料</th>
                        </tr>
                        <tr>
                            <th width=\"25%\">名稱</th>
                            <th width=\"25%\">說明</th>
                            <th width=\"25%\">更新時間</th>
                            <th width=\"25%\">建立時間</th>
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
                                "</tr>", $campaign_setting['title'],
                                         $campaign_setting['description'],
                                         $campaign_setting['updated_at'],
                                         $campaign_setting['created_at']);
                return str_replace("{ROWS}", implode('', $rows), $str);

            }, '優惠活動');

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
        return Admin::form(Exchanges::class, function (Form $form) {

            $form->switch('status', '上架否')->states($this->status_arr)->default(1);
        });
    }
}
