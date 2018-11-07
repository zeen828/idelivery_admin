<?php

namespace App\Admin\Controllers\Management;

//Model
use App\Models\idelivery\Admin_notice;
use App\Models\idelivery\Company;
use App\Models\idelivery\Store;

//預載功能
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//額外增加
use Encore\Admin\Widgets\Box;

class AdminNoticeController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'off' => array('value' => 0, 'text' => '停止', 'color' => 'default'),
        'on'  => array('value' => 1, 'text' => '啟用', 'color' => 'primary'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('商店管理 > 系統公告');
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

            $content->header('商店管理 > 系統公告');
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

            $content->header('商店管理 > 系統公告');
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
        return Admin::grid(Admin_notice::class, function (Grid $grid) {

            $grid->id('ID')->sortable();

            $grid->column('model', '類型')->display(function ($model) {
                switch ($model) {
                    case 0:
                        return '一般訊息';
                        break;
                    case 1:
                        return '系統通知';
                        break;
                    case 2:
                        return '更新訊息';
                        break;
                    default:
                        return $model;
                }
            });
            $grid->column('company.name', '品牌');
            $grid->column('store.name', '店家');
            $grid->column('title', '標題');
            $grid->column('start_at', '起始時間');
            $grid->column('end_at', '結束時間');
            $grid->column('status', '狀態')->switch($this->status_arr);
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Admin_notice::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->radio('model', '類型')->options([0=>'一般訊息', 1=>'系統通知', 2=>'更新訊息'])->rules('required');
            $form->select('company_id', '品牌')->options(Company::pluck('name', 'id'))->default('0');
            $form->select('store_id', '店家')->options(Store::pluck('name', 'id'))->default('0');
            $form->text('title', '標題')->rules('required');
            $form->textarea('desc', '描述')->rows(10);
            $form->text('url', '網址');
            $form->datetimeRange('start_at', 'end_at', '公告時間')->rules('required');//活動設定有範例

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
