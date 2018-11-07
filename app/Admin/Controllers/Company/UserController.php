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
use Encore\Admin\Widgets\Box;
use Encore\Admin\Widgets\Table;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Facades\Session;

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
            $company_id = Session::get('company_id');
            $content->header(trans('idelivery.user.config') . 'test');
            $content->description(trans('idelivery.admin.index'));
            if(empty($company_id)) {
                $box1 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box1->removable()->style('warning'));
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
            $stoer_id = Session::get('store_id');
            $content->header(trans('idelivery.user.config'));
            $content->description(trans('idelivery.admin.edit'));
            // 檢查是不是屬於用戶可以編輯
            $admin_user = Admin_users::find($id)->store->where('id', '=', $stoer_id);
            if($admin_user->isEmpty()){
                $box1 = new Box(trans('idelivery.box.not_for_you.title'), trans('idelivery.box.not_for_you.content'));
                $content->row($box1->removable()->style('danger'));
            }else{
                if(empty($company_id)) {
                    $box1 = new Box(trans('idelivery.box.not_company.title'), trans('idelivery.box.not_company.content'));
                    $content->row($box1->removable()->style('warning'));
                }else {
                    $content->body($this->form()->edit($id));
                }
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
            $content->header(trans('idelivery.user.config'));
            $content->description(trans('idelivery.admin.create'));
            if(empty($company_id)) {
                $box1 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box1->removable()->style('warning'));
            }else {
                $content->body($this->form());
            }
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
            $stoer_id = Session::get('store_id');
            $grid->model()->getCompanyUser($stoer_id);
            //$grid->model()->getStoeeUser($stoer_id);

            // 禁止功能
            //$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('name', '名稱');
            $grid->column('username', '帳號');
            $grid->roles('角色')->pluck('title')->label();

            $grid->created_at();
            $grid->updated_at();

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

            $login_id = Admin::user()->id;
            $company_id = Session::get('company_id');
            //$store_id = Session::get('store_id');
            $store_id = 0;

            $form->hidden('id');
            $form->hidden('admin_user_id')->default($login_id);
            $form->mobile('username', trans('admin.username'))->options(['mask' => '9999999999'])->placeholder('請輸入帳號')->rules('required');;
            //$form->text('username', trans('admin.username'))->placeholder('請輸入帳號')->rules('required');
            $form->text('name', trans('admin.name'))->placeholder('請輸入名稱')->rules('required');
            $form->password('password', trans('admin.password'))->placeholder('請輸入')->rules('required|confirmed');
            $form->password('password_confirmation', trans('admin.password_confirmation'))->rules('required')
                ->default(function ($form) {
                    return $form->model()->password;
                });
            // 不處理這欄位
            $form->ignore(['password_confirmation']);
            // 多對多角色
            $form->multipleSelect('roles', '角色')->options(
                Admin_roles::where('company_id', $company_id)->where('store_id', $store_id)
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
                unset($count);
                unset($username);
                // 處理密碼
                if ($form->password && $form->model()->password != $form->password) {
                    $form->password = bcrypt($form->password);
                }
            });
            $form->saved(function (Form $form) {
                $user_id = $form->model()->id;
                // 建立使用者與店家的關聯
                $stoer_id = Session::get('store_id');
                //var_dump($stoer_id);
                //取得店家所屬總店
                $stoer = Store::find($stoer_id);
                //var_dump($stoer);
                $head_store_id = $stoer->head_store_id;
                $now_date = date('Y-m-d h:i:s');
                DB::table('admin_user_store')->insert(
                    ['user_id' => $user_id, 'store_id' => $head_store_id, 'created_at' => $now_date, 'updated_at' =>$now_date]
                );
                unset($now_date);
                unset($head_store_id);
                unset($stoer);
                unset($store_id);
                unset($user_id);
            });
        });
    }

    public function log()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header(trans('idelivery.admin.log'));
            $content->description(trans('idelivery.admin.log'));
            if(empty($company_id)) {
                $box1 = new Box('提示', '請選擇所屬店家!!');
                $content->row($box1->removable()->style('warning'));
            }else{
                // 店家
                $store_id = Session::get('store_id');
                $stoer = Store::find($store_id);
                $head_store_id = $stoer->head_store_id;
                // LOG紀錄
                $headers = ['Id', '帳號', '方式', '路徑', '輸入資料', '時間'];
                $rows = [];
                $users_logs = DB::table('admin_user_store')->select('admin_operation_log.*', 'admin_users.username')
                    ->leftJoin('admin_users', 'admin_users.id', '=', 'admin_user_store.user_id')
                    ->leftJoin('admin_operation_log', 'admin_operation_log.user_id', '=', 'admin_user_store.user_id')
                    ->where('admin_user_store.store_id', $head_store_id)
                    ->orderBy('admin_operation_log.created_at', 'desc')
                    ->paginate();
                //->limit(10)->get();
                //$users_logs->withPath('/admin/company/user');
                foreach ($users_logs as $key=>$log) {
                    $rows[] = [
                        $log->id,
                        $log->username,
                        $log->method,
                        $log->path,
                        $log->input,
                        $log->created_at,
                    ];
                }
                //var_dump($users_logs->render());
                $table = new Table($headers, $rows);
                $box2 = new Box('Forth Box', $table . '<div style="margin: 0px auto;">' . $users_logs->render() . '</div>');
                //$box2 = new Box('Forth Box', $table);
                $content->row($box2->removable()->style('warning'));
            }
        });
    }
}
