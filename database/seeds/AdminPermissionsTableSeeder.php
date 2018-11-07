<?php

use Illuminate\Database\Seeder;

class AdminPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=AdminPermissionsTableSeeder
        // 第一次執行,執行前全部資料庫清空
        $now_date = date('Y-m-d h:i:s');
        // 權限
        DB::table('admin_permissions')->truncate();
        DB::table('admin_permissions')->insert([
            //system
            //可用號碼1~6(使用到:6)
            ['id' => '1', 'name' => 'All permission', 'slug' => '*', 'http_method' => '', 'http_path' => "*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '2', 'name' => '儀表板', 'slug' => 'dashboard', 'http_method' => 'GET', 'http_path' => "/dashboard\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '3', 'name' => '登入登出', 'slug' => 'auth.login', 'http_method' => '', 'http_path' => "/auth/login\r\n/auth/logout\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '4', 'name' => '會員設定', 'slug' => 'auth.setting', 'http_method' => 'GET,PUT', 'http_path' => "/auth/setting\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '5', 'name' => 'Auth management', 'slug' => 'auth.management', 'http_method' => '', 'http_path' => "/auth/roles\r\n/auth/permissions\r\n/auth/menu\r\n/auth/logs\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '6', 'name' => 'Admin helpers', 'slug' => 'ext.helpers', 'http_method' => '', 'http_path' => "/helpers/*", 'created_at' => $now_date, 'updated_at' => $now_date],
            //system共用
            //可用號碼7~8(使用到:8)
            ['id' => '7', 'name' => '共用[首頁](功能前導)', 'slug' => 'index', 'http_method' => 'GET', 'http_path' => "/\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '8', 'name' => '共用[店家切換]', 'slug' => 'system.change', 'http_method' => 'GET,POST', 'http_path' => "/system/change_config\r\n/system/change_config/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台
            //可用號碼51~100(使用到:58)
            ['id' => '51', 'name' => '網站管理員[總部品牌設定]', 'slug' => 'management.company', 'http_method' => '', 'http_path' => "/management/set/company\r\n/management/set/company/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '52', 'name' => '網站管理員[總部店家設定]', 'slug' => 'management.store', 'http_method' => '', 'http_path' => "/management/set/store\r\n/management/set/store/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '53', 'name' => '網站管理員[總部店家設定>新增帳號]', 'slug' => 'management.store.user', 'http_method' => '', 'http_path' => "/management/set_company_store_user/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '54', 'name' => '網站管理員[總部使用者設定]', 'slug' => 'management.user', 'http_method' => '', 'http_path' => "/management/set/user\r\n/management/set/user/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '55', 'name' => '網站管理員[營業設定]', 'slug' => 'management.config.business', 'http_method' => '', 'http_path' => "/management/set/cuisine_category\r\n/management/set/cuisine_category/*\r\n/management/set/cuisine_type\r\n/management/set/cuisine_type/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '56', 'name' => '網站管理員[品牌設定檔上傳]', 'slug' => 'management.config.company', 'http_method' => '', 'http_path' => "/management/set/app_config\r\n/management/set/app_config/*\r\n/system/config_upload\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '57', 'name' => '網站管理員[店家設定檔上傳]', 'slug' => 'management.config.store', 'http_method' => '', 'http_path' => "/management/set/store_config\r\n/management/set/store_config/*\r\n/system/store_config_upload\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '58', 'name' => '網站管理員[系統公告]', 'slug' => 'management.config.store', 'http_method' => '', 'http_path' => "/management/set/notice\r\n/management/set/notice/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台報表
            //company總公司
            //可用號碼101~400(使用到:112)
            ['id' => '101', 'name' => '品牌管理員[店家管理]', 'slug' => 'company.store', 'http_method' => '', 'http_path' => "/company/set/store\r\n/company/set/store/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '102', 'name' => '品牌管理員[店家管理>新增帳號]', 'slug' => 'company.store.user', 'http_method' => '', 'http_path' => "/company/set_store_user/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '103', 'name' => '品牌管理員[餐點管理>餐點分類]', 'slug' => 'company.menu', 'http_method' => '', 'http_path' => "/company/set/cuisine_group\r\n/company/set/cuisine_group/*\r\n/company/set_group_item\r\n/company/set_group_item/*\r\n/company/set/menu_item\r\n/company/set/menu_item/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '104', 'name' => '品牌管理員[餐點管理>附加選項分類]', 'slug' => 'company.menu_option', 'http_method' => '', 'http_path' => "/company/set/cuisine_unit\r\n/company/set/cuisine_unit/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '105', 'name' => '品牌管理員[品牌設定]', 'slug' => 'company.setting', 'http_method' => '', 'http_path' => "/company/set/carousel\r\n/company/set/carousel/*\r\n/company/set/banner\r\n/company/set/banner/*\r\n/company/set/news\r\n/company/set/news/*\r\n/company/set/aboutus\r\n/company/set/aboutus/*\r\n/company/set/product\r\n/company/set/product/*\r\n/company/set/qa\r\n/company/set/qa/*\r\n/company/set/others\r\n/company/set/others/*\r\n/system/image_upload\r\n/system/image_upload/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '106', 'name' => '品牌管理員[行銷活動設定>點數相關>(舊)點數活動設定]', 'slug' => 'company.point', 'http_method' => '', 'http_path' => "/company/set/promotion\r\n/company/set/promotion/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '107', 'name' => '品牌管理員[行銷活動設定>點數相關>兌換商品設定]', 'slug' => 'company.point.exchanges', 'http_method' => '', 'http_path' => "/company/set/exchanges\r\n/company/set/exchanges/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '108', 'name' => '品牌管理員[行銷活動設定>活動相關>註冊禮]', 'slug' => 'company.event.reg', 'http_method' => '', 'http_path' => "/company/set/campaign/reg/none/coupon\r\n/company/set/campaign/reg/none/coupon/*\r\n/company/set/campaign/reg/none/points\r\n/company/set/campaign/reg/none/points/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '109', 'name' => '品牌管理員[行銷活動設定>活動相關>結帳優惠活動]', 'slug' => 'company.event.order', 'http_method' => '', 'http_path' => "/company/set/campaign/overview\r\n/company/set/campaign/overview/*\r\n/company/set/campaign/index\r\n/company/set/campaign/index/*\r\n/company/set/campaign/order\r\n/company/set/campaign/order/*\r\n/company/set/campaign/order/cash/amount\r\n/company/set/campaign/order/cash/amount/*\r\n/company/set/campaign/order/cash/discount\r\n/company/set/campaign/order/cash/discount/*\r\n/company/set/campaign/order/cash/qty\r\n/company/set/campaign/order/cash/qty/*\r\n/company/set/campaign/order/amount/amount\r\n/company/set/campaign/order/amount/amount/*\r\n/company/set/campaign/order/amount/qty\r\n/company/set/campaign/order/amount/qty/*\r\n/company/set/campaign/order/amount/discount\r\n/company/set/campaign/order/amount/discount/*\r\n/company/set/campaign/order/qty/qty\r\n/company/set/campaign/order/qty/qty/*\r\n/company/set/campaign/order/qty/amount\r\n/company/set/campaign/order/qty/amount/*\r\n/company/set/campaign/order/qty/discount\r\n/company/set/campaign/order/qty/discount/*\r\n/company/set/campaign/order/item/amount\r\n/company/set/campaign/order/item/amount/*\r\n/company/set/campaign/order/item/discount\r\n/company/set/campaign/order/item/discount/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '110', 'name' => '品牌管理員[行銷活動設定>活動相關>交易完成]', 'slug' => 'company.event.order_over', 'http_method' => '', 'http_path' => "#", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '111', 'name' => '品牌管理員[行銷活動設定>優惠券相關]', 'slug' => 'company.coupon', 'http_method' => '', 'http_path' => "/company/set/coupon/overview\r\n/company/set/coupon/overview/*\r\n/company/set/coupon/index\r\n/company/set/coupon/index/*\r\n/company/set/coupon/order\r\n/company/set/coupon/order/*\r\n/company/set/coupon/order/amount/amount\r\n/company/set/coupon/order/amount/amount/*\r\n/company/set/coupon/order/amount/discount\r\n/company/set/coupon/order/amount/discount/*\r\n/company/set/coupon/order/amount/qty\r\n/company/set/coupon/order/amount/qty/*\r\n/company/set/coupon/order/qty/amount\r\n/company/set/coupon/order/qty/amount/*\r\n/company/set/coupon/order/qty/discount\r\n/company/set/coupon/order/qty/discount/*\r\n/company/set/coupon/order/qty/qty\r\n/company/set/coupon/order/qty/qty/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '112', 'name' => '品牌管理員[角色帳號管理]', 'slug' => 'company.role_user', 'http_method' => '', 'http_path' => "/company/set/role\r\n/company/set/role/*\r\n/company/set/user\r\n/company/set/user/*\r\n/logs/company/user\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司報表
            //可用號碼401~500(使用到:401)
            ['id' => '401', 'name' => '品牌管理員[統計報表]', 'slug' => 'company.role_user', 'http_method' => '', 'http_path' => "#", 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家
            //可用號碼501~800(使用到:512)
            ['id' => '501', 'name' => '店家管理員[訂單列表]', 'slug' => 'store.order', 'http_method' => '', 'http_path' => "/store/orders\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '502', 'name' => '店家管理員[店家設定]', 'slug' => 'store.setting', 'http_method' => '', 'http_path' => "/store/set/store\r\n/store/set/store/*\r\n/store/profile\r\n/store/profile/*\r\n/store/set/poshow\r\n/store/set/poshow/*\r\n/store/set/publish/poshow\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '503', 'name' => '店家管理員[餐點管理>餐點分類]', 'slug' => 'store.menu', 'http_method' => '', 'http_path' => "/store/set/cuisine_group\r\n/store/set/cuisine_group/*\r\n/store/set_group_item\r\n/store/set_group_item/*\r\n/store/set/menu_item/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '504', 'name' => '店家管理員[餐點管理>附加選項分類]', 'slug' => 'store.menu_option', 'http_method' => '', 'http_path' => "/store/set/cuisine_unit\r\n/store/set/cuisine_unit/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '505', 'name' => '店家管理員[餐點管理>蔡單管理]', 'slug' => 'store.menu.api', 'http_method' => '', 'http_path' => "/store/set/menu_import\r\n/store/set/menu_import/*\r\n/store/set/menu_import/import_all\r\n/store/set/publish/menu\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '506', 'name' => '店家管理員[行銷活動設定>點數相關>點數管理]', 'slug' => 'store.point', 'http_method' => '', 'http_path' => "/store/point\r\n/store/point/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '507', 'name' => '店家管理員[行銷活動設定>點數相關>兌換商品設定]', 'slug' => 'store.point.exchanges', 'http_method' => '', 'http_path' => "/store/set/exchanges\r\n/store/set/exchanges/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '508', 'name' => '店家管理員[行銷活動設定>活動相關>註冊禮]', 'slug' => 'store.event.reg', 'http_method' => '', 'http_path' => "#", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '509', 'name' => '店家管理員[行銷活動設定>活動相關>結帳優惠活動]', 'slug' => 'store.event.order', 'http_method' => '', 'http_path' => "/store/set/campaign/overview\r\n/store/set/campaign/overview/*\r\n/store/set/campaign/index\r\n/store/set/campaign/index/*\r\n/store/set/campaign/order\r\n/store/set/campaign/order/*\r\n/store/set/campaign/order/cash/amount\r\n/store/set/campaign/order/cash/amount/*\r\n/store/set/campaign/order/cash/discount\r\n/store/set/campaign/order/cash/discount/*\r\n/store/set/campaign/order/cash/qty\r\n/store/set/campaign/order/cash/qty/*\r\n/store/set/campaign/order/amount/amount\r\n/store/set/campaign/order/amount/amount/*\r\n/store/set/campaign/order/amount/qty\r\n/store/set/campaign/order/amount/qty/*\r\n/store/set/campaign/order/amount/discount\r\n/store/set/campaign/order/amount/discount/*\r\n/store/set/campaign/order/qty/qty\r\n/store/set/campaign/order/qty/qty/*\r\n/store/set/campaign/order/qty/amount\r\n/store/set/campaign/order/qty/amount/*\r\n/store/set/campaign/order/qty/discount\r\n/store/set/campaign/order/qty/discount/*\r\n/store/set/campaign/order/item/amount\r\n/store/set/campaign/order/item/amount/*\r\n/store/set/campaign/order/item/discount\r\n/store/set/campaign/order/item/discount/*\r\n/store/set/publish/campaign\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '510', 'name' => '店家管理員[行銷活動設定>活動相關>交易完成]', 'slug' => 'store.event.order_over', 'http_method' => '', 'http_path' => "#", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '511', 'name' => '店家管理員[行銷活動設定>優惠券相關]', 'slug' => 'store.coupon', 'http_method' => '', 'http_path' => "/store/set/coupon/overview\r\n/store/set/coupon/overview/*\r\n/store/set/coupon/index\r\n/store/set/coupon/index/*\r\n/store/set/coupon/order\r\n/store/set/coupon/order/*\r\n/store/set/coupon/order/amount/amount\r\n/store/set/coupon/order/amount/amount/*\r\n/store/set/coupon/order/amount/discount\r\n/store/set/coupon/order/amount/discount/*\r\n/store/set/coupon/order/amount/qty\r\n/store/set/coupon/order/amount/qty/*\r\n/store/set/coupon/order/qty/amount\r\n/store/set/coupon/order/qty/amount/*\r\n/store/set/coupon/order/qty/discount\r\n/store/set/coupon/order/qty/discount/*\r\n/store/set/coupon/order/qty/qty\r\n/store/set/coupon/order/qty/qty/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '512', 'name' => '店家管理員[角色帳號管理]', 'slug' => 'store.role_user', 'http_method' => '', 'http_path' => "/store/set/role\r\n/store/set/role/*\r\n/store/set/user\r\n/store/set/user/*\r\n/store/set/pos_account\r\n/store/set/pos_account/*\r\n/logs/store/user\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家報表
            //可用號碼801~900(使用到:801)
            ['id' => '801', 'name' => '店家管理員[統計報表]', 'slug' => 'store.menu', 'http_method' => '', 'http_path' => "/store/reports/statistics/*\r\n/store/reports/daily_sale/*\r\n/store/reports/period_sale/*\r\n/store/reports/item_sale/*\r\n/store/reports/group_sale/*\r\n/store/reports/campaign/*\r\n/store/reports/order_type/*\r\n/store/reports/payment/*\r\n", 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);

        //重設基礎角色的權限
        //DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->whereIn('role_id', [1, 2, 3, 4])->delete();
        DB::table('admin_role_permissions')->insert([
            //admin系統
            ['role_id' => 1, 'permission_id' => 1, 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台
            ['role_id' => 2, 'permission_id' => 2, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 3, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 4, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 7, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 8, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 51, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 52, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 53, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 54, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 55, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 56, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 57, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 2, 'permission_id' => 58, 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司
            ['role_id' => 3, 'permission_id' => 2, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 3, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 4, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 7, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 8, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 101, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 102, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 103, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 104, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 105, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 106, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 107, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 108, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 109, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 110, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 111, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 112, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 3, 'permission_id' => 401, 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家
            ['role_id' => 4, 'permission_id' => 2, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 3, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 4, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 7, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 8, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 501, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 502, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 503, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 504, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 505, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 506, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 507, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 508, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 509, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 510, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 511, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 512, 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => 4, 'permission_id' => 801, 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        //權限配下去
        $roles = DB::table('admin_roles')->select('id', 'admin_role_id')->whereNotIn('id', [1, 2, 3, 4])->whereIn('admin_role_id', [3, 4])->get();
        if (!$roles->isEmpty()){
            foreach ($roles as $role){
                //print_r($role);
                //SELECT `role_id`, count(*) FROM `admin_role_permissions` WHERE 1 GROUP BY `role_id`
                //echo '[', $role->id, ']', "\n";
                DB::table('admin_role_permissions')->where('role_id', '=', $role->id)->delete();
                $sql = sprintf("INSERT INTO admin_role_permissions (role_id, permission_id, created_at, updated_at) SELECT %s, permission_id, now(), now() FROM admin_role_permissions WHERE role_id = %s;", $role->id, $role->admin_role_id);
                //echo $sql, "\n";
                DB::select($sql);
            }
        }
    }
}
