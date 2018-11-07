<?php

namespace App\Admin\Controllers\Company;

use DB;
use App\Http\Controllers\Controller;
use App\Model\idelivery\Admin_users;
use App\Model\idelivery\Admin_roles;
use App\Model\idelivery\Store;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Http\Request;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Session;

class StoreUserController extends Controller
{
    use ModelForm;

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index(Request $request)
    {
        return Admin::content(function (Content $content) {

            $content->header(trans('idelivery.user.config'));
            $content->description(trans('idelivery.admin.index'));

            $content->body($this->grid());
        });
    }

    /**
     * Edit interface.
     *
     * @param $id
     * @return Content
     */
    public function edit($id, Request $request)
    {
        //$store_id = $request->segment(4);
        $store_id = $request->store_id;
        Session::put('management_store_id', $store_id);
        return Admin::content(function (Content $content) use ($id) {

            $content->header(trans('idelivery.user.config'));
            $content->description(trans('idelivery.admin.edit'));

            $content->body($this->form()->edit($id));
        });
    }

    /**
     * Create interface.
     *
     * @return Content
     */
    public function create(Request $request)
    {
        //$store_id = $request->segment(4);
        $store_id = $request->store_id;
        Session::put('management_store_id', $store_id);
        return Admin::content(function (Content $content) {

            $content->header(trans('idelivery.user.config'));
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

            // 禁止功能
            //$grid->disableCreation();//創建
            $grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            $grid->disableActions();//操作

            $grid->id('ID')->sortable();

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
        return Admin::form(Admin_users::class, function (Form $form) {

            $login_id = Admin::user()->id;
            $stoer_id = Session::get('management_store_id');
            $stoer = Store::find($stoer_id);
            $company_id = $stoer->company_id;

            $form->hidden('id');
            $form->hidden('admin_user_id')->default($login_id);

            $form->mobile('username', trans('admin.username'))->options(['mask' => '9999999999'])->placeholder('請輸入帳號')->rules('required');;
            //$form->text('username', trans('admin.username'))->placeholder('請輸入帳號')->rules('required');
            $form->text('name', trans('admin.name'))->placeholder('請輸入...')->rules('required');
            $form->password('password', trans('admin.password'))->placeholder('請輸入')->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            // 不處理這欄位
            $form->ignore(['password_confirmation']);
            // 多對多角色
            $form->multipleSelect('roles', '角色')->options(
                Admin_roles::where('company_id', $company_id)->where('store_id', $stoer_id)
                    ->pluck('title', 'id')
            )->rules('required');

            $form->hidden('created_at');
            $form->hidden('updated_at');

            $form->saving(function (Form $form) {
                // 帳號重複
                $username = $form->username;
                $count = Admin_users::where('username', '=', $username)->count();
                if($count >= 1){
                    // 輸出異常
                    //throw new \Exception('該總店品牌已設定過總部店家!!');
                    // 輸出錯誤訊息
                    $error = new MessageBag([
                        'title'   => '發生錯誤',
                        //'message' => '該帳號已被使用過!!',
                        'message' => sprintf("該%s已被使用過!!", trans('idelivery.user.field.username')),
                    ]);
                    return back()->with(compact('error'));
                }
                // 處理密碼
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
            $form->saved(function (Form $form) {
                $now_date = date('Y-m-d h:i:s');
                $user_id = $form->model()->id;
                $store_id = Session::get('management_store_id');
                // 建立使用者與店家的關聯
                DB::table('admin_user_store')->insert(
                    ['user_id' => $user_id, 'store_id' => $store_id, 'created_at' => $now_date, 'updated_at' =>$now_date]
                );
                //清理SESSION
                Session::forget('management_store_id');
                //跳轉頁面
                return redirect('/admin/company/set/store');
            });
        });
    }
}
