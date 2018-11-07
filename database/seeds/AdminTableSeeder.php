<?php

use Illuminate\Database\Seeder;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=AdminTableSeeder
        // 第一次執行,執行前全部資料庫清空
        $now_date = date('Y-m-d h:i:s');
        // 會員
        // DB::table('admin_users')->truncate();
        DB::table('admin_users')->insert([
            ['id' => '1', 'admin_user_id' => '0', 'username' => 'admin', 'password' => '$2y$10$hR.BLlFF.GLIp70.VMAY1ucH63a9J4KPSM3dlwodAY2YVpuHIhPpC', 'name' => '網站管理員', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '2', 'admin_user_id' => '0', 'username' => 'webadmin', 'password' => '$2y$10$NHdn7yiXOcp04Cyit82loOU8qICFYrOTx8qhyuVw5hlq3/lNkb7Qe', 'name' => '網站管理員', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        // 角色
        // DB::table('admin_roles')->truncate();
        DB::table('admin_roles')->insert([
            ['id' => '1', 'company_id' => '0', 'store_id' => '0', 'admin_role_id' => '0', 'name' => '系統管理員', 'title' => '系統管理員', 'slug' => 'administrator', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '2', 'company_id' => '0', 'store_id' => '0', 'admin_role_id' => '2', 'name' => '網站管理員', 'title' => '網站管理員', 'slug' => 'webadmin', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '3', 'company_id' => '0', 'store_id' => '0', 'admin_role_id' => '3', 'name' => '總店管理員', 'title' => '總店管理員', 'slug' => 'company', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '4', 'company_id' => '0', 'store_id' => '0', 'admin_role_id' => '4', 'name' => '店家管理員', 'title' => '店家管理員', 'slug' => 'store', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        // 角色&會員關係
        // DB::table('admin_role_users')->truncate();
        DB::table('admin_role_users')->insert([
            ['role_id' => '1', 'user_id' => '1', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'user_id' => '2', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        // 角色&權限關係
        // DB::table('admin_role_permissions')->truncate();
        DB::table('admin_role_permissions')->insert([
            //admin
            ['role_id' => '1', 'permission_id' => '1', 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台
            ['role_id' => '2', 'permission_id' => '2', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'permission_id' => '3', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'permission_id' => '4', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'permission_id' => '7', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'permission_id' => '9', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'permission_id' => '10', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'permission_id' => '11', 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司
            ['role_id' => '3', 'permission_id' => '2', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '3', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '4', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '7', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '8', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '12', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '13', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '14', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '15', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '16', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '17', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '3', 'permission_id' => '18', 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家
            ['role_id' => '4', 'permission_id' => '2', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '3', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '4', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '7', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '8', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '19', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '20', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '21', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '22', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '4', 'permission_id' => '23', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        // 角色&目錄關係
        // DB::table('admin_role_menu')->truncate();
        DB::table('admin_role_menu')->insert([
            //admin
            ['role_id' => '1', 'menu_id' => '2', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '1', 'menu_id' => '8', 'created_at' => $now_date, 'updated_at' => $now_date],
            //management大後台
            ['role_id' => '2', 'menu_id' => '13', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['role_id' => '2', 'menu_id' => '14', 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司
            ['role_id' => '3', 'menu_id' => '15', 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家
            ['role_id' => '4', 'menu_id' => '16', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
        // 會員&店家
        // DB::table('admin_user_store')->truncate();
    }
}
