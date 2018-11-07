<?php

namespace App\Admin\Controllers\Management;

//Model
use App\Model\idelivery\Admin_users;

//預載功能
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;

//額外增加
use Encore\Admin\Widgets\Box;

class UserController extends Controller
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

            $content->header('商店管理 > ' . trans('idelivery.user.config'));
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

            $content->header('商店管理 > ' . trans('idelivery.user.config'));
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

            $content->header('商店管理 > ' . trans('idelivery.user.config'));
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
        return Admin::grid(Admin_users::class, function (Grid $grid) {
            // 設定條件
            $grid->model()->getManagementUser();

            // 禁止功能
            $grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('username', trans('idelivery.user.field.username'));
            $grid->column('name', trans('idelivery.user.name'));
            $grid->store(trans('idelivery.store.name'))->pluck('name')->label();

            $grid->created_at(trans('idelivery.admin.field.created_at'));
            $grid->updated_at(trans('idelivery.admin.field.updated_at'));

//            $grid->actions(function ($actions) {
//                // append一个操作
//                $actions->append('<a href=""><i class="fa fa-eye"></i></a>');
//            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Admin_users::class, function (Form $form) {

            $form->hidden('id');

            $form->password('password', trans('admin.password'))->placeholder('請輸入...')->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->placeholder('請輸入...')->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            // 不處理這欄位
            $form->ignore(['password_confirmation']);

            $form->saving(function (Form $form) {
                // 處理密碼
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
        });
    }
}
