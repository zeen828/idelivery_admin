<?php

namespace App\Admin\Controllers\Company;

use DB;
use App\Http\Controllers\Controller;
use App\Model\idelivery\Admin_users;
use App\Model\idelivery\Company;
use App\Model\idelivery\Store;
use App\Model\idelivery\District_tw;
use App\Lib\Geocoding;
use Encore\Admin\Form;
use Encore\Admin\Grid;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Layout\Content;
use Encore\Admin\Controllers\ModelForm;
use Encore\Admin\Widgets\Box;
use Illuminate\Support\Facades\Session;

class StoreController extends Controller
{
    use ModelForm;

    private $status_arr = array(
        'on'  => array('value' => 1, 'text' => '正常', 'color' => 'primary'),
        'off' => array('value' => 2, 'text' => '關閉', 'color' => 'default'),
    );

    private $switch_arr = array(
        'off' => array('value' => 0, 'text' => '關閉', 'color' => 'default'),
        'on'  => array('value' => 1, 'text' => '開啟', 'color' => 'primary'),
    );

    /**
     * Index interface.
     *
     * @return Content
     */
    public function index()
    {
        return Admin::content(function (Content $content) {
            $company_id = Session::get('company_id');
            $content->header(trans('idelivery.store.config'));
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

            $store = Store::find($id);
            if (empty($store) || empty($store->company_id) || $company_id != $store->company_id)
            {
                $warning = new Box('警告', '您無權限訪問本頁面 !');
                $content->row($warning->removable()->style('warning'));
                return false;
            }

            $content->header(trans('idelivery.store.config'));
            $content->description(trans('idelivery.admin.edit'));
            if(empty($company_id)) {
                $box3 = new Box('提示', sprintf("請選擇所屬%s!!", trans('idelivery.store.title')));
                $content->row($box3->removable()->style('warning'));
            }else{
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
            $content->header(trans('idelivery.store.config'));
            $content->description(trans('idelivery.admin.create'));
            if(empty($company_id)) {
                $box3 = new Box('提示', sprintf("請選擇所屬%s!!", trans('idelivery.store.title')));
                $content->row($box3->removable()->style('warning'));
            }else{
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
        return Admin::grid(Store::class, function (Grid $grid) {
            // 設定條件
            $company_id = Session::get('company_id');
            $grid->model()->where('company_id', '=', $company_id);

            // 禁止功能
            //$grid->disableCreation();//創建
            //$grid->disablePagination();//分頁
            $grid->disableFilter();//查詢
            $grid->disableExport();//匯出
            $grid->disableRowSelector();//多選
            //$grid->disableActions();//操作

            $grid->id('ID')->sortable();

            $grid->column('model_vip', 'VIP');
            $grid->column('name', trans('idelivery.store.name'));
            $grid->column('business_registration', '營業登記名稱');
            $grid->column('uniform_numbers', '統編');
            $grid->column('address', '店面地址');
            $grid->column('supervisor_name', '加盟主/店長');
            $grid->column('supervisor_phone', '加盟主/店長手機');
            $grid->column('model_account', '分店管理帳號')->display(function ($title) {
                $admin_user = Admin_users::getStoeeAdminUsers($this->id);
                $html = '';
                if(!$admin_user->isEmpty()){
                    foreach($admin_user as $user){
                        $html .= sprintf("%s<br/>", $user->username);
                    }
                }
                return $html;
            });

            $grid->column('status', '狀態')->switch($this->status_arr);

            //$grid->created_at();
            //$grid->updated_at();

            $grid->actions(function ($actions) {
                //var_dump($actions->row->id);
                $store_id = $actions->row->id;
                $actions->append('<a href="/admin/company/set_store_user/' . $store_id . '/user/create" class="fa fa-user-plus"></a>');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Admin::form(Store::class, function (Form $form) {

            $company_id = Session::get('company_id');
            $stoer_id = Session::get('store_id');
            //取得店家所屬總店
            $stoer = Store::find($stoer_id);
            $head_store_id = $stoer->head_store_id;

            $form->hidden('id');
            $form->hidden('company_id')->default($company_id);
            $form->hidden('head_store_id', '總公司店家ID')->default($head_store_id);
            $form->tab('店家設定', function ($form) use ($stoer_id) {//特別帶店家ID近來組S3目錄
                $form->text('name', trans('idelivery.store.name'))->rules('required');
                $form->image('image', '示意圖')->uniqueName()->rules('nullable||max:900')
                    ->move(env('ADMIN_UPLOAD_PATH', '') . 'store/image/sign/' . $stoer_id);
                $form->text('business_registration', '營業登記名稱');
                $form->text('uniform_numbers', '統編');
                $form->text('intro_url', '店家介紹網址');//->rules('required');
                $form->text('order_phone', '訂購電話')->help('顯示在app的點餐單裡的訂餐需知');//->rules('required');
                $form->switch('status', '狀態')->states($this->status_arr)->default('1');
            })->tab('聯絡設定', function ($form) {
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
                $form->hidden('latitude', '緯度');
                $form->hidden('longitude', '經度');
                $form->text('address', '地址');
                $form->text('supervisor_name', '加盟主/店長姓名');
                $form->text('supervisor_phone', '加盟主/店長手機');
                $form->text('supervisor_email', '加盟主/店長email');
                //$form->text('uniform_numbers', '分店管理帳號')->placeholder('請輸入分店管理帳號');
//            })->tab('開關設定', function($form) {
//                $form->switch('sw_app', 'App顯示開關')->states($this->switch_arr)->default(1);
//                $form->switch('sw_pos', 'Pos顯示開關')->states($this->switch_arr)->default(1);
//                $form->switch('sw_reward', '獎勵開關')->states($this->switch_arr)->default('0');
//                $form->switch('sw_r_point', '獎勵集點開關')->states($this->switch_arr)->default('0');
//                $form->switch('sw_r_campaign', '獎勵活動開關')->states($this->switch_arr)->default('0');
//                $form->switch('sw_use', '使用開關')->states($this->switch_arr)->default('0');
//                $form->switch('sw_u_exchange', '使用兌換開關')->states($this->switch_arr)->default('0');
//                $form->switch('sw_u_campaign', '使用活動開關')->states($this->switch_arr)->default('0');
//                $form->switch('sw_u_coupon', '使用優惠卷開關')->states($this->switch_arr)->default('0');
            });
            $form->hidden('create_time');
            $form->hidden('updated_at');

            $form->saving(function (Form $form) {
                // 地址追加紀錄資料
                $district_id = $form->district_id;// 直接判斷物件會錯亂所以存變數
                $address = $form->address;
                $district_name = false;
                if(!empty($district_id)){
                    $district = District_tw::find((int) $district_id);
                    $form->city_id = $district->city_id;
                    $form->post_code = $district->post_code;
                    $form->district_name = $district->name;
                    $district_name = $district->name;
                }
                //有區域或地址就查詢經緯度
                if(!empty($district_name) && !empty($address)){
                    //經緯度
                    $Geocoding = new Geocoding;
                    $result = $Geocoding->addr2latlng(sprintf("%s%s", $district->name, $address));

                    $form->latitude  = 0;
                    $form->longitude = 0;
                    if ($result !== false) {
                        $form->latitude  = $result['lat'];
                        $form->longitude = $result['lng'];
                    }

                    unset($result);
                    unset($Geocoding);
                }
            });
            $form->saved(function (Form $form) {
                // 只在新增處理
                if(request()->isMethod('POST')) {
                    $now_date = date('Y-m-d h:i:s');
                    $company_id = $form->company_id;
                    $store_id = $form->model()->id;
                    $company_name = $form->model()->company->name;
                    $store_name = $form->model()->name;
                    // 店家要繼承複製的角色
                    $store_extends_role_id = config('damaiapp.extends_role.store_role_id');
                    // 複製店家最高權限
                    $role_id = DB::table('admin_roles')->insertGetId(
                        ['company_id' => $company_id, 'store_id' => $store_id, 'admin_role_id' => $store_extends_role_id, 'name' => sprintf("系統複製S%03d店家管理員%s", $store_id, rand('00', '99')), 'title' => '店家最高權限', 'slug' => sprintf("store_%03d_management", $store_id), 'created_at' => $now_date, 'updated_at' => $now_date]
                    );

                    if(!empty($role_id)){
                        // 角色&目錄
                        DB::select('INSERT INTO admin_role_menu (role_id, menu_id) SELECT ?, menu_id FROM admin_role_menu WHERE role_id = ?;', [$role_id, $store_extends_role_id]);
                        // 角色&權限
                        DB::select('INSERT INTO admin_role_permissions (role_id, permission_id) SELECT ?, permission_id FROM admin_role_permissions WHERE role_id = ?;', [$role_id, $store_extends_role_id]);
                    }

                    // 建立賣家管理角色權限
                    DB::table('admin_account_store')->insert([
                        [
                            'name' => '店長',
                            'store_id' => $store_id,
                            'store_name' => $store_name,
                            'admin_id' => 1,
                            'rules' => json_encode(array("on_site_order", "order_manager", "order_finder", "setting", "close_store", "settle_accounts", "invoice_setting")),
                            'allow_process' => json_encode(array(1,3,4,6)),
                            'create_time' => $now_date
                        ],
                        [
                            'name' => '外送員',
                            'store_id' => $store_id,
                            'store_name' => $store_name,
                            'admin_id' => 2,
                            'rules' => json_encode(array("driver_area")),
                            'allow_process' => json_encode(array(4,5,6)),
                            'create_time' => $now_date
                        ]                        
                    ]);
                }
            });
        });
    }
}
