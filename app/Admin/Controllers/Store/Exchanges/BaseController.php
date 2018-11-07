<?php

namespace App\Admin\Controllers\Store\Exchanges;

use App\Models\idelivery\Exchanges;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class BaseController extends Controller
{
    use ModelForm;

    // 上架下架 switch
    protected $status_arr = array(
        'on'  => array('value' => 1, 'text' => '上架', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '下架', 'color' => 'default'),
    );

    protected $service_id = null;   // ServiceId
    protected $company_id = null;   // 品牌Id
    protected $store_id   = null;   // 商家Id
    protected $exchanges_type = null;   // 兌換商品類型
    protected $config = ['index' => ['description'=> '']]; // 清單描述

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = $this->company_id;
            $store_id   = $this->store_id;
            $content->header('兌換設定');
            $content->description($this->config['index']['description']);
            if(empty($company_id) || empty($store_id)) {
                $box = new Box('提示', '請選擇店家!!');
                $content->row($box->removable()->style('warning'));
            }else{
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
        Session::put('exchanges_id', $id);

        return Admin::content(function (Content $content) use ($id) {
            $company_id = $this->company_id;
            $store_id   = $this->store_id;

            $exchanges = Exchanges::find($id);
            if (empty($exchanges)
                || empty($exchanges->company_id)
                || $company_id != $exchanges->company_id
                || empty($exchanges->store_id)
                || $store_id != $exchanges->store_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('活動設定');
            $content->description('兌換商品設定');

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
            $content->header('活動設定');
            $content->description('兌換商品設定');

            $content->body($this->form());
        });
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    public function grid()
    {
        return Admin::grid(Exchanges::class, function (Grid $grid) {
            $service_id = $this->service_id;
            $company_id = $this->company_id;
            $store_id   = $this->store_id;
            $exchanges_type = $this->exchanges_type;

            $grid->model()
                    ->where('service_id', $service_id)
                    ->where('company_id', $company_id)
                    ->where('store_id', $store_id)
                    ->where('exchanges_type', $exchanges_type)
                    ->orderBy('id', 'asc');
            $grid->column('name', '商品名稱')->style('width:20%');
            $grid->column('image', '商品圖')->image(env('ADMIN_UPLOAD_URL', ''), 50)->style('width:5%');
            $grid->column('description', '商品說明')->style('width:30%');
            $grid->column('point', '點數')->style('text-align: right;width:5%');
            $grid->column('stock', '可兌換量')->style('text-align: right;width:5%');

            // date format
            $cb = function ($date){return date("Y-m-d", strtotime($date));};
            $grid->column('start_date', '起始日期')->display($cb)->style('width:10%');
            $grid->column('end_date',   '結束日期')->display($cb)->style('width:10%');
            $grid->column('status', '上架否')->switch($this->status_arr)->style('width:5%');
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
            $service_id = $this->service_id;
            $company_id = $this->company_id;
            $store_id   = $this->store_id;
            $exchanges_type = $this->exchanges_type;

            $form->hidden('service_id','服務編號')->default($service_id);
            $form->hidden('company_id','公司編號')->default($company_id);
            $form->hidden('store_id','店家編號')->default($store_id);
            $form->hidden('point_type_id','類別')->default(1);
            $form->hidden('exchanges_type', '兌換商品類型')->default($exchanges_type);
            $form->hidden('id','兌換商品編號');
            $form->text('name', '商品名稱')->rules('required|max:25')->placeholder('限25字');
            $form->image('image', '商品圖')->uniqueName()->rules('nullable||max:900')
                ->move(env('ADMIN_UPLOAD_PATH', '') . 'company/image/exchanges/' . $company_id);
            $form->text('description', '商品說明')->rules('required');
            $form->number('point', '點數')->rules('required|min:0')->default(0);
            $form->number('stock', '可兌換量')->rules('required|min:0');
            $form->datetime('start_date', '起始日期')->format('YYYY-MM-DD 00:00:00');
            $form->datetime('end_date', '結束日期')->format('YYYY-MM-DD 23:59:59');
            $form->switch('status', '上架否')->states($this->status_arr)->default(1);

            $form = $this->addAdditionalFormField($form);
        });
    }

    /**
     * Add additional form field
     *
     * @return Form
     */
    protected function addAdditionalFormField(Form $form)
    {
        return $form;
    }

}
