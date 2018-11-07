<?php

namespace App\Admin\Controllers\Store;

use App\Http\Controllers\Controller;
use App\Model\idelivery\Admin_account;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class DeliveryStaffController extends Controller
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
            $content->header('外送員帳號管理');
            $content->description(trans('idelivery.admin.index'));
            if (empty($company_id)) {
                $box1 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box1->removable()->style('warning'));
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
            $store_id = Session::get('store_id');
            $admin_account = Admin_account::find($id);
            if (empty($admin_account) || empty($admin_account->store_id)
                || $store_id != $admin_account->store_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header('外送員帳號管理');
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

            $content->header('外送員帳號管理');
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
        return Admin::grid(Admin_account::class, function (Grid $grid) {
            // 設定條件
            $stoer_id = Session::get('store_id');
            $grid->model()->where('store_id', '=', $stoer_id);

            // 禁止功能
            //$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('company.name', '帳號');
            $grid->column('store.name', '帳號');
            $grid->column('account', '帳號');
            $grid->column('admin_account_store.name', '帳號');

            $grid->created_at();
            $grid->updated_at();
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Admin_account::class, function (Form $form) {

            $form->display('id', 'ID');

            $form->display('created_at', 'Created At');
            $form->display('updated_at', 'Updated At');
        });
    }
}
