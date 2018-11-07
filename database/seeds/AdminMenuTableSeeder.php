.<?php

use Illuminate\Database\Seeder;

class AdminMenuTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=AdminMenuTableSeeder
        // 可以直接增加沒問題,後面在調整權限
        $now_date = date('Y-m-d h:i:s');
        // 目錄
        DB::table('admin_menu')->truncate();
        DB::table('admin_menu')->insert([
            //首頁
            ['id' => '1', 'parent_id' => '0', 'order' => '1', 'title' => 'Index', 'icon' => 'fa-dashboard', 'uri' => '/', 'created_at' => $now_date, 'updated_at' => $now_date],
            //system
            ['id' => '2', 'parent_id' => '0', 'order' => '501', 'title' => '系統管理', 'icon' => 'fa-users', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '3', 'parent_id' => '2', 'order' => '3', 'title' => '用戶管理', 'icon' => 'fa-users', 'uri' => 'auth/users', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '4', 'parent_id' => '2', 'order' => '4', 'title' => '角色管理', 'icon' => 'fa-user', 'uri' => 'auth/roles', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '5', 'parent_id' => '2', 'order' => '5', 'title' => '權限管理', 'icon' => 'fa-ban', 'uri' => 'auth/permissions', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '6', 'parent_id' => '2', 'order' => '6', 'title' => '選單管理', 'icon' => 'fa-bars', 'uri' => 'auth/menu', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '7', 'parent_id' => '2', 'order' => '7', 'title' => 'Operation log', 'icon' => 'fa-history', 'uri' => 'auth/logs', 'created_at' => $now_date, 'updated_at' => $now_date],
            //Helpers
            ['id' => '8', 'parent_id' => '0', 'order' => '801', 'title' => 'Helpers', 'icon' => 'fa-gears', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '9', 'parent_id' => '8', 'order' => '9', 'title' => 'Scaffold', 'icon' => 'fa-keyboard-o', 'uri' => 'helpers/scaffold', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '10', 'parent_id' => '8', 'order' => '10', 'title' => 'Database terminal', 'icon' => 'fa-database', 'uri' => 'helpers/terminal/database', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '11', 'parent_id' => '8', 'order' => '11', 'title' => 'Laravel artisan', 'icon' => 'fa-terminal', 'uri' => 'helpers/terminal/artisan', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '12', 'parent_id' => '8', 'order' => '12', 'title' => 'Routes', 'icon' => 'fa-list-alt', 'uri' => 'helpers/routes', 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台
            //可用號碼13~50(使用到:22)
            ['id' => '13', 'parent_id' => '0', 'order' => '2', 'title' => '商店管理', 'icon' => 'fa-cutlery', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '14', 'parent_id' => '13', 'order' => '14', 'title' => trans('idelivery.company.config')/*'總部品牌設定'*/, 'icon' => 'fa-bars', 'uri' => 'management/set/company', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '15', 'parent_id' => '13', 'order' => '15', 'title' => trans('idelivery.store.config')/*'總部總店設定'*/, 'icon' => 'fa-bars', 'uri' => 'management/set/store', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '16', 'parent_id' => '13', 'order' => '16', 'title' => trans('idelivery.user.config')/*'總店帳號管理'*/, 'icon' => 'fa-bars', 'uri' => 'management/set/user', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '17', 'parent_id' => '13', 'order' => '17', 'title' => '營業設定', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '18', 'parent_id' => '17', 'order' => '18', 'title' => '營業類別', 'icon' => 'fa-bars', 'uri' => 'management/set/cuisine_category', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '19', 'parent_id' => '17', 'order' => '19', 'title' => '營業項目', 'icon' => 'fa-bars', 'uri' => 'management/set/cuisine_type', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '20', 'parent_id' => '13', 'order' => '20', 'title' => '品牌設定檔上傳', 'icon' => 'fa-upload', 'uri' => 'management/set/app_config', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '21', 'parent_id' => '13', 'order' => '21', 'title' => '店家設定檔上傳', 'icon' => 'fa-upload', 'uri' => 'management/set/store_config', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '22', 'parent_id' => '13', 'order' => '22', 'title' => '系統公告', 'icon' => 'fa-upload', 'uri' => 'management/set/notice', 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台報表
            //可用號碼51~100(使用到:54)
            ['id' => '51', 'parent_id' => '0', 'order' => '8', 'title' => '財務管理', 'icon' => 'fa-bitcoin', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '52', 'parent_id' => '51', 'order' => '52', 'title' => '帳單總覽', 'icon' => 'fa-bars', 'uri' => '/', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '53', 'parent_id' => '51', 'order' => '53', 'title' => '品牌營收總表', 'icon' => 'fa-bars', 'uri' => '/', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '54', 'parent_id' => '51', 'order' => '54', 'title' => '單店月結報表', 'icon' => 'fa-bars', 'uri' => '/', 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司
            //可用號碼101~400(使用到:138)
            ['id' => '101', 'parent_id' => '0', 'order' => '13', 'title' => trans('idelivery.company.admin')/*'總部管理'*/, 'icon' => 'fa-copyright', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '102', 'parent_id' => '101', 'order' => '102', 'title' => trans('idelivery.store.admin')/*'分店列表'*/, 'icon' => 'fa-sitemap', 'uri' => 'company/set/store', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '103', 'parent_id' => '101', 'order' => '103', 'title' => trans('idelivery.cuisine.admin')/*'公版餐點管理'*/, 'icon' => 'fa-cutlery', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '104', 'parent_id' => '103', 'order' => '104', 'title' => trans('idelivery.cuisine.group.title')/*'餐點分類'*/, 'icon' => 'fa-cutlery', 'uri' => 'company/set/cuisine_group', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '105', 'parent_id' => '103', 'order' => '105', 'title' => trans('idelivery.cuisine.unit.title')/*'選項管理'*/, 'icon' => 'fa-cutlery', 'uri' => 'company/set/cuisine_unit', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '106', 'parent_id' => '101', 'order' => '106', 'title' => trans('idelivery.company.config')/*'品牌設定'*/, 'icon' => 'fa-cogs', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '107', 'parent_id' => '106', 'order' => '107', 'title' => '首頁輪播', 'icon' => 'fa-cog', 'uri' => 'company/set/carousel', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '108', 'parent_id' => '106', 'order' => '108', 'title' => '點餐大圖', 'icon' => 'fa-cog', 'uri' => 'company/set/banner', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '109', 'parent_id' => '106', 'order' => '109', 'title' => '活動訊息', 'icon' => 'fa-cog', 'uri' => 'company/set/news', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '110', 'parent_id' => '106', 'order' => '110', 'title' => '關於我們', 'icon' => 'fa-cog', 'uri' => 'company/set/aboutus', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '111', 'parent_id' => '106', 'order' => '111', 'title' => '餐點介紹', 'icon' => 'fa-cog', 'uri' => 'company/set/product', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '112', 'parent_id' => '106', 'order' => '112', 'title' => '常見問題', 'icon' => 'fa-cog', 'uri' => 'company/set/qa', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '113', 'parent_id' => '106', 'order' => '113', 'title' => '其他設定', 'icon' => 'fa-cog', 'uri' => 'company/set/others', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '115', 'parent_id' => '101', 'order' => '115', 'title' => '行銷活動設定', 'icon' => 'fa-gift', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '116', 'parent_id' => '115', 'order' => '116', 'title' => '點數相關', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '117', 'parent_id' => '116', 'order' => '117', 'title' => '(舊)點數活動設定', 'icon' => 'fa-bars', 'uri' => 'company/set/promotion', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '118', 'parent_id' => '116', 'order' => '118', 'title' => '點數活動設定(未做)', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '119', 'parent_id' => '116', 'order' => '119', 'title' => '兌換商品設定', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '137', 'parent_id' => '119', 'order' => '137', 'title' => '總覽', 'icon' => 'fa-bars', 'uri' => 'company/set/exchanges/overview', 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '138', 'parent_id' => '119', 'order' => '138', 'title' => '引導頁', 'icon' => 'fa-bars', 'uri' => 'company/set/exchanges/guide', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '120', 'parent_id' => '115', 'order' => '120', 'title' => '活動相關', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '121', 'parent_id' => '120', 'order' => '121', 'title' => '註冊禮', 'icon' => 'fa-user-plus', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '122', 'parent_id' => '121', 'order' => '122', 'title' => '贈送優惠券', 'icon' => 'fa-newspaper-o', 'uri' => 'company/set/campaign/reg/none/coupon', 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '123', 'parent_id' => '121', 'order' => '123', 'title' => '贈送點數', 'icon' => 'fa-bars', 'uri' => 'company/set/campaign/reg/none/points', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '124', 'parent_id' => '120', 'order' => '124', 'title' => '結帳優惠活動', 'icon' => 'fa-shopping-cart', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '125', 'parent_id' => '124', 'order' => '125', 'title' => '總覽', 'icon' => 'fa-bars', 'uri' => 'company/set/campaign/overview', 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '126', 'parent_id' => '124', 'order' => '126', 'title' => '引導頁', 'icon' => 'fa-bars', 'uri' => 'company/set/campaign/index', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '127', 'parent_id' => '120', 'order' => '127', 'title' => '交易完成', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '128', 'parent_id' => '127', 'order' => '128', 'title' => '贈送優惠券(未做)', 'icon' => 'fa-newspaper-o', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '129', 'parent_id' => '127', 'order' => '129', 'title' => '贈送點數(未做)', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '130', 'parent_id' => '115', 'order' => '130', 'title' => '優惠卷相關', 'icon' => 'fa-newspaper-o', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '131', 'parent_id' => '130', 'order' => '131', 'title' => '總覽', 'icon' => 'fa-bars', 'uri' => 'company/set/coupon/overview', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '132', 'parent_id' => '130', 'order' => '132', 'title' => '引導頁', 'icon' => 'fa-bars', 'uri' => 'company/set/coupon/index', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '133', 'parent_id' => '101', 'order' => '133', 'title' => '角色帳號管理', 'icon' => 'fa-group', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '134', 'parent_id' => '133', 'order' => '134', 'title' => trans('idelivery.role.config')/*'角色管理'*/, 'icon' => 'fa-group', 'uri' => 'company/set/role', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '135', 'parent_id' => '133', 'order' => '135', 'title' => trans('idelivery.user.config')/*'帳號管理'*/, 'icon' => 'fa-group', 'uri' => 'company/set/user', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '136', 'parent_id' => '133', 'order' => '136', 'title' => trans('idelivery.admin.log')/*'帳號LOG'*/, 'icon' => 'fa-group', 'uri' => 'logs/company/user', 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司報表
            //可用號碼401~500(使用到:401)
            ['id' => '401', 'parent_id' => '0', 'order' => '51', 'title' => '品牌報表', 'icon' => 'fa-bar-chart', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家
            //可用號碼501~800(使用到:533)
            ['id' => '501', 'parent_id' => '0', 'order' => '104', 'title' => trans('idelivery.store.admin')/*'分店管理'*/, 'icon' => 'fa-sitemap', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '502', 'parent_id' => '501', 'order' => '502', 'title' => '訂單及結帳單', 'icon' => 'fa-bars', 'uri' => 'store/orders', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '530', 'parent_id' => '502', 'order' => '530', 'title' => '訂單列表', 'icon' => 'fa-bars', 'uri' => 'store/list/orders', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '531', 'parent_id' => '502', 'order' => '531', 'title' => '結帳單列表', 'icon' => 'fa-bars', 'uri' => 'store/list/billings', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '503', 'parent_id' => '501', 'order' => '503', 'title' => trans('idelivery.store.config')/*'商店設定'*/, 'icon' => 'fa-cogs', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '504', 'parent_id' => '503', 'order' => '504', 'title' => '商店基本資料', 'icon' => 'fa-cog', 'uri' => 'store/set/store', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '505', 'parent_id' => '503', 'order' => '505', 'title' => '商店店面設定', 'icon' => 'fa-cog', 'uri' => 'store/profile', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '529', 'parent_id' => '503', 'order' => '529', 'title' => 'POS子母畫面', 'icon' => 'fa-cog', 'uri' => 'store/set/poshow', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '506', 'parent_id' => '501', 'order' => '506', 'title' => trans('idelivery.cuisine.admin')/*'餐點管理'*/, 'icon' => 'fa-cutlery', 'uri' => 'company', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '507', 'parent_id' => '506', 'order' => '507', 'title' => trans('idelivery.cuisine.group.title')/*'餐點分類'*/, 'icon' => 'fa-cutlery', 'uri' => 'store/set/cuisine_group', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '508', 'parent_id' => '506', 'order' => '508', 'title' => trans('idelivery.cuisine.unit.title')/*'選項管理'*/, 'icon' => 'fa-cutlery', 'uri' => 'store/set/cuisine_unit', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '509', 'parent_id' => '506', 'order' => '509', 'title' => trans('idelivery.menu.admin')/*'餐點清單'*/, 'icon' => 'fa-cutlery', 'uri' => 'store/set/menu_import', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '510', 'parent_id' => '501', 'order' => '510', 'title' => '行銷活動設定', 'icon' => 'fa-gift', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '511', 'parent_id' => '510', 'order' => '511', 'title' => '點數相關', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '512', 'parent_id' => '511', 'order' => '512', 'title' => '點數管理'/*'點數管理'*/, 'icon' => 'fa-bars', 'uri' => 'store/point', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '513', 'parent_id' => '511', 'order' => '513', 'title' => '兌換商品設定', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '532', 'parent_id' => '513', 'order' => '532', 'title' => '總覽', 'icon' => 'fa-bars', 'uri' => 'store/set/exchanges/overview', 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '533', 'parent_id' => '513', 'order' => '533', 'title' => '引導頁', 'icon' => 'fa-bars', 'uri' => 'store/set/exchanges/guide', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '514', 'parent_id' => '510', 'order' => '514', 'title' => '活動相關', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '515', 'parent_id' => '514', 'order' => '515', 'title' => '結帳優惠活動', 'icon' => 'fa-shopping-cart', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '516', 'parent_id' => '515', 'order' => '516', 'title' => '總覽', 'icon' => 'fa-bars', 'uri' => 'store/set/campaign/overview', 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '517', 'parent_id' => '515', 'order' => '517', 'title' => '引導頁', 'icon' => 'fa-bars', 'uri' => 'store/set/campaign/index', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '518', 'parent_id' => '514', 'order' => '518', 'title' => '交易完成', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '519', 'parent_id' => '518', 'order' => '519', 'title' => '贈送優惠券(未做)', 'icon' => 'fa-newspaper-o', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                            ['id' => '520', 'parent_id' => '518', 'order' => '520', 'title' => '贈送點數(未做)', 'icon' => 'fa-bars', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '521', 'parent_id' => '510', 'order' => '521', 'title' => '優惠卷相關', 'icon' => 'fa-newspaper-o', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '522', 'parent_id' => '521', 'order' => '522', 'title' => '總覽', 'icon' => 'fa-bars', 'uri' => 'store/set/coupon/overview', 'created_at' => $now_date, 'updated_at' => $now_date],
                        ['id' => '523', 'parent_id' => '521', 'order' => '523', 'title' => '引導頁', 'icon' => 'fa-bars', 'uri' => 'store/set/coupon/index', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '524', 'parent_id' => '501', 'order' => '524', 'title' => '角色帳號管理', 'icon' => 'fa-group', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '525', 'parent_id' => '524', 'order' => '525', 'title' => trans('idelivery.role.config')/*'角色管理'*/, 'icon' => 'fa-group', 'uri' => 'store/set/role', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '526', 'parent_id' => '524', 'order' => '526', 'title' => trans('idelivery.user.config')/*'帳號管理'*/, 'icon' => 'fa-group', 'uri' => 'store/set/user', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '527', 'parent_id' => '524', 'order' => '527', 'title' => trans('idelivery.pos.config')/*'賣家管理角色'*/, 'icon' => 'fa-group', 'uri' => 'store/set/pos_account', 'created_at' => $now_date, 'updated_at' => $now_date],
                    ['id' => '528', 'parent_id' => '524', 'order' => '528', 'title' => trans('idelivery.admin.log')/*'帳號LOG'*/, 'icon' => 'fa-group', 'uri' => 'logs/store/user', 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家報表
            //可用號碼801~900(使用到:812)
            ['id' => '801', 'parent_id' => '0', 'order' => '401', 'title' => '店家報表', 'icon' => 'fa-bar-chart', 'uri' => NULL, 'created_at' => $now_date, 'updated_at' => $now_date],
                //['id' => '802', 'parent_id' => '801', 'order' => '802', 'title' => '訂單銷售', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/statistics/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '803', 'parent_id' => '801', 'order' => '803', 'title' => '日期銷售', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/daily_sale/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '804', 'parent_id' => '801', 'order' => '804', 'title' => '時段銷售', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/period_sale/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '805', 'parent_id' => '801', 'order' => '805', 'title' => '餐點品項銷售', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/item_sale/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '806', 'parent_id' => '801', 'order' => '806', 'title' => '餐點分類銷售', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/group_sale/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '807', 'parent_id' => '801', 'order' => '807', 'title' => '折扣類型', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/campaign/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                ['id' => '808', 'parent_id' => '801', 'order' => '808', 'title' => '訂單類型', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/order_type/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                //['id' => '809', 'parent_id' => '801', 'order' => '809', 'title' => '付款方式', 'icon' => 'fa-area-chart', 'uri' => 'store/reports/payment/query', 'created_at' => $now_date, 'updated_at' => $now_date],
                //['id' => '810', 'parent_id' => '801', 'order' => '810', 'title' => '會員分級銷售(還沒好)', 'icon' => 'fa-area-chart', 'uri' => '#',/*'store/reports/member',*/ 'created_at' => $now_date, 'updated_at' => $now_date],
                //['id' => '811', 'parent_id' => '801', 'order' => '811', 'title' => '天氣(還沒好)', 'icon' => 'fa-area-chart', 'uri' => '#',/*'store/reports/weather',*/ 'created_at' => $now_date, 'updated_at' => $now_date],
                //['id' => '812', 'parent_id' => '801', 'order' => '812', 'title' => '報表寄送對象設定(還沒好)', 'icon' => 'fa-area-chart', 'uri' => '#',/*'store/reports/send_setting',*/ 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        //新號碼規則
        //每一區給一個區間使用餐到個區塊註解
        //用完再給他第二個區間

        //角色目錄關係
        DB::table('admin_role_menu')->whereIn('role_id', [1, 2, 3, 4])->delete();
        DB::table('admin_role_menu')->insert([
            // 1:系統管理員
            ['role_id' => '1', 'menu_id' => '1', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '2', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '8', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '13', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '51', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '101', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '401', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '501', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '801', 'created_at' => $now_date, 'updated_at' => $now_date],
            // 2:網站管理員
            ['role_id' => '2', 'menu_id' => '1', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'menu_id' => '13', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'menu_id' => '51', 'created_at' => $now_date, 'updated_at' => $now_date],
            // 3:總店管理員
            ['role_id' => '3', 'menu_id' => '1', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'menu_id' => '101', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'menu_id' => '401', 'created_at' => $now_date, 'updated_at' => $now_date],
            // 4:店家管理員
            ['role_id' => '4', 'menu_id' => '1', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'menu_id' => '501', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'menu_id' => '801', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        //角色目錄分配下去(只分給最大權限)
        $roles = DB::table('admin_roles')->select('id', 'admin_role_id')->whereNotIn('id', [1, 2, 3, 4])->whereIn('admin_role_id', [3, 4])->get();
        if (!$roles->isEmpty()){
            foreach ($roles as $role){
                //print_r($role);
                //SELECT `role_id`, count(*) FROM `admin_role_permissions` WHERE 1 GROUP BY `role_id`
                //echo '[', $role->id, ']', "\n";
                DB::table('admin_role_menu')->where('role_id', '=', $role->id)->delete();
                $sql = sprintf("INSERT INTO admin_role_menu (role_id, menu_id, created_at, updated_at) SELECT %s, menu_id, now(), now() FROM admin_role_menu WHERE role_id = %s;", $role->id, $role->admin_role_id);
                //echo $sql, "\n";
                DB::select($sql);
            }
        }
    }
}
