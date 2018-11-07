<?php

namespace App\Admin\Controllers\Company;

use DB;
use App\Http\Controllers\Controller;
use App\Model\idelivery\Admin_roles;
use App\Model\idelivery\Admin_permissions;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class RoleController extends Controller
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
            $content->header(trans('idelivery.role.config'));
            $content->description(trans('idelivery.admin.index'));
            if(empty($company_id)) {
                $box3 = new Box('提示', sprintf("請選擇所屬%s!!", trans('idelivery.store.title')));
                $content->row($box3->removable()->style('warning'));
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
        return Admin::content(function (Content $content) use ($id) {
            $company_id = Session::get('company_id');

            $roles = Admin_roles::find($id);
            if (empty($roles) || empty($roles->company_id)
                || $company_id != $roles->company_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header(trans('idelivery.role.config'));
            $content->description(trans('idelivery.admin.edit'));
            if(empty($company_id)) {
                $box3 = new Box('提示', sprintf("請選擇所屬%s!!", trans('idelivery.store.title')));
                $content->row($box3->removable()->style('warning'));
            }else {
                $content->body($this->form()->edit($id));
            }
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
            $company_id = Session::get('company_id');
            $content->header(trans('idelivery.role.config'));
            $content->description(trans('idelivery.admin.create'));
            if(empty($company_id)) {
                $box3 = new Box('提示', sprintf("請選擇所屬%s!!", trans('idelivery.store.title')));
                $content->row($box3->removable()->style('warning'));
            }else {
                $content->body($this->form());
            }
        });
    }

//    public function store()
//    {
//        var_dump('OOXX');
//        var_dump($_POST);
//    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Admin::grid(Admin_roles::class, function (Grid $grid) {
            // 設定條件
            $company_id = Session::get('company_id');
            //$store_id = Session::get('store_id');
            $store_id = 0;
            $grid->model()->where('company_id', '=', $company_id)->where('store_id', '=', $store_id);

            // 禁止功能
            //$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('title', trans('idelivery.role.name'));
            //$grid->permissions('權限')->pluck('name')->label();
            $grid->permissions(trans('idelivery.permissions.title'))->display(function ($permissions) {
                $html_str = '';
                foreach($permissions as $permission) {
                    $html_str .= sprintf("<span class='label label-success'>%s</span><br/>", $permission['name']);
                }
                return $html_str;
            });

            $grid->created_at(trans('idelivery.admin.field.created_at'));
            $grid->updated_at(trans('idelivery.admin.field.updated_at'));
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Admin_roles::class, function (Form $form) {

            // 總部要繼承複製的角色
            $company_extends_role_id = config('damaiapp.extends_role.company_role_id');
            $form->hidden('id');

            $company_id = Session::get('company_id');
            $form->hidden('company_id')->default($company_id);
            //$store_id = Session::get('store_id');
            $store_id = 0;
            $form->hidden('store_id')->default($store_id);
            $form->hidden('admin_role_id')->default($company_extends_role_id);
            $form->hidden('name')->default(sprintf("%s%03d新增%s%s", trans('idelivery.store.title'), $company_id, trans('idelivery.role.title'), time()));
            $form->text('title', trans('idelivery.role.name'))->placeholder('請輸入...')->rules('required');
            $form->hidden('slug')->default(sprintf("company_%03d_%s", $company_id, str_random(5)));
            // 多對多關聯權限
            //$form->listbox('permissions', trans('admin.permissions'))->options(Admin_permissions::all()->pluck('name', 'id'));
            $form->listbox('permissions', trans('idelivery.permissions.title'))->options(
                DB::table('admin_role_permissions')
                    ->leftJoin('admin_permissions', 'admin_permissions.id', '=', 'admin_role_permissions.permission_id')
                    ->where('admin_role_permissions.role_id', $company_extends_role_id)
                    ->orderBy('admin_permissions.id', 'asc')
                    ->pluck('admin_permissions.name', 'admin_permissions.id')
            )->settings(['selectorMinimalHeight' => 200]);

            $form->hidden('created_at');
            $form->hidden('updated_at');
            $form->saved(function (Form $form) {
                $role_id = $form->model()->id;
                // 總部要繼承複製的角色
                $company_extends_role_id = config('damaiapp.extends_role.company_role_id');
                DB::select('DELETE FROM admin_role_menu WHERE role_id = ?;', [$role_id]);
                // 建立角色與目錄關係
                DB::select('INSERT INTO admin_role_menu (role_id, menu_id) SELECT ?, menu_id FROM admin_role_menu WHERE role_id = ?;', [$role_id, $company_extends_role_id]);
            });
        });
    }
}
