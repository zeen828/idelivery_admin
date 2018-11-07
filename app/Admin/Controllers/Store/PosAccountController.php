<?php

namespace App\Admin\Controllers\Store;

use App\Models\idelivery\Admin_account;
use App\Models\idelivery\Admin_account_store;
use App\Models\idelivery\Company;
use App\Models\idelivery\Store;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;

class PosAccountController extends Controller
{
    use ModelForm;

    private $states = [
        'on'  => ['value' => '1', 'text' => '啟用', 'color' => 'primary'],
        'off' => ['value' => '2', 'text' => '關閉', 'color' => 'default'],
    ];

    private $first_login = [
        'on'  => ['value' => '1', 'text' => '是', 'color' => 'primary'],
        'off' => ['value' => '0', 'text' => '否', 'color' => 'default'],
    ];

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {

            $content->header('賣家管理');
            $content->description('帳號角色設定');

            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            if (empty($company_id) || empty($store_id)) {
                $box1 = new Box('警告', '您無權限訪問本頁面!!');
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

            $content->header('賣家管理');
            $content->description('帳號角色設定');

            $store_id = Session::get('store_id');
            $admin_account = Admin_account::find($id);
            if (empty($admin_account) || empty($admin_account->store_id) || $store_id != $admin_account->store_id) {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                
                return false;
            }

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

            $content->header('賣家管理');
            $content->description('帳號角色設定');

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

            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            $grid->model()->where('company_id', $company_id)->where('store_id', $store_id);

            // $grid->id('ID')->sortable();
            $grid->account('登入帳號');
            $grid->column('登入角色')->display(function() {
                return Admin_account_store::find($this->admin_account_store_id)->name;
            });

            $grid->name('顯示名稱');
            $grid->status('狀態')->switch($this->states);
            $grid->first_login('是否第一次登入')->switch($this->first_login);

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

            $company_id = Session::get('company_id');
            $store_id   = Session::get('store_id');

            $form->hidden('company_id')->default($company_id);
            //$form->hidden('company_name')->default(Company::find($company_id)->name);
            $form->hidden('store_id')->default($store_id);
            //$form->hidden('store_name')->default(Store::find($store_id)->name);

            $form->text('company_name', trans('idelivery.company.name'));
            $form->text('store_name', trans('idelivery.store.name'));
            $form->text('account', '登入帳號')->rules('required');
            $form->password('password', trans('admin.password'))->rules('confirmed|min:6');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->default(function($form) {
                return $form->model()->password;
            });
            // 不處理這欄位
            $form->ignore(['password_confirmation']);
            $form->text('name', '顯示名稱')->rules('required');
            $form->select('admin_account_store_id', '角色')
                    ->options(Admin_account_store::where('store_id', $store_id)->pluck('name', 'id'));

            $form->switch('first_login', '第一次登入是否需要修改密碼')->states($this->first_login)->default('1');
            $form->switch('status', '狀態')->states($this->states)->default('1');

            $form->saving(function(Form $form) {
                $value = $form->password;
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = password_hash($value, PASSWORD_BCRYPT, array("cost" => 12));
                }
            });
        });
    }
}
