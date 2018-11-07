<?php

namespace App\Admin\Controllers\Management;

//Model
use App\Model\idelivery\Company;
use App\Model\idelivery\District_tw;

//預載功能
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//額外增加
use Encore\Admin\Widgets\Box;

class CompanyController extends Controller
{
    use ModelForm;
    public $status_arr = array(
        'on'  => array('value' => 1, 'text' => '正常', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商店管理 > ' . trans('idelivery.company.config'));
            $content->description(trans('idelivery.admin.index'));

            //幫助
            $box1 = new Box(trans('idelivery.box.help.title'), '內容');
            $content->row($box1->collapsable());

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

            $content->header('商店管理 > ' . trans('idelivery.company.config'));
            $content->description(trans('idelivery.admin.edit'));

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

            $content->header('商店管理 > ' . trans('idelivery.company.config'));
            $content->description(trans('idelivery.admin.create'));

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
        return Admin::grid(Company::class, function (Grid $grid) {
            // 禁止功能
            ///$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('brand', trans('idelivery.company.name'));
            $grid->column('name', '營業登記名稱');
            $grid->column('uniform_numbers', '統編');
            $grid->column('model_store_count', '分店數')->display(function ($title) {
                return "0";
            });
            $grid->column('model_user_count', '會員數')->display(function ($title) {
                return "0";
            });
            $grid->column('model_app_count', 'APP下載數')->display(function ($title) {
                return "0";
            });
            $grid->column('status', '狀態')->switch($this->status_arr);

            //$grid->created_at();
            //$grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Company::class, function (Form $form) {
            $form->hidden('id');
            $form->tab('品牌設定', function ($form) {
                $form->text('brand', trans('idelivery.company.name'))->placeholder('請輸入...')->rules('required');
                $form->text('name', '營業登記名稱')->placeholder('請輸入...')->rules('required');
                $form->image('image', '示意圖')->uniqueName()->rules('max:900')
                    ->move(env('ADMIN_UPLOAD_PATH', '') . 'company/image/sign');
                $form->text('uniform_numbers', '統編')->placeholder('請輸入...');
                $form->switch('status', '狀態')->states($this->status_arr)->default('1');
            })->tab('聯絡設定', function ($form) {
                $form->text('supervisor_name', '聯絡人')->placeholder('請輸入...');
                $form->mobile('supervisor_phone', '電話')->placeholder('請輸入...')->options(["mask"=>"9999999999"]);
                $form->select('district_id', '區域')->options(function(){
                    $district = District_tw::select('id', 'name')->get();
                    $option = [];
                    foreach ($district as $row){
                        $option[$row->id] = $row->name;
                    }
                    return $option;
                });
                $form->hidden('city_id', '城市');
                $form->hidden('post_code', '郵遞區號');
                $form->hidden('district_name', '區域');
                $form->text('address', '地址')->placeholder('請輸入...');
                $form->textarea('remarks', '備註')->rows(5)->placeholder('請輸入...');
            })->tab('帳務設定', function ($form) {
                $form->text('profit', '拆帳比')->placeholder('請輸入...')->default('0.0');
                $form->text('bank', '銀行名稱')->placeholder('請輸入...');
                $form->text('bank_branch', '銀行分行')->placeholder('請輸入...');
                $form->text('bank_account', '銀行帳號')->placeholder('請輸入...');
                $form->image('passbook_picture', '銀行存摺')->uniqueName()->rules('max:900')
                    ->move(env('ADMIN_UPLOAD_PATH', '') . 'company/passbook/');
            });

            $form->hidden('create_time');
            $form->hidden('updated_at');

            $form->saving(function (Form $form) {
                // 地址追加紀錄資料
                $district_id = $form->district_id;// 直接判斷物件會錯亂所以存變數
                if(!empty($district_id)){
                    $district = District_tw::find((int) $district_id);
                    $form->city_id = $district->city_id;
                    $form->post_code = $district->post_code;
                    $form->district_name = $district->name;
                    unset($district);
                }
                unset($district_id);
            });
        });
    }
}
