<?php

use Illuminate\Database\Seeder;

class AddAdminPermissionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=AddAdminPermissionsTableSeeder
        $now_date = date('Y-m-d h:i:s');
        // 角色&權限關係
        DB::table('admin_role_permissions')->insert([
            //management大後台
            ['role_id' => '2', 'permission_id' => '24', 'created_at' => $now_date, 'updated_at' => $now_date],
            //company總公司
            ['role_id' => '3', 'permission_id' => '25', 'created_at' => $now_date, 'updated_at' => $now_date],
            //store店家
            ['role_id' => '4', 'permission_id' => '26', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
    }
}
