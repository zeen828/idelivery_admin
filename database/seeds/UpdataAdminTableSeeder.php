<?php

use Illuminate\Database\Seeder;

class UpdataAdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=UpdataAdminTableSeeder
        $now_date = date('Y-m-d h:i:s');
        // 權限調整
        DB::table('admin_permissions')->where('id', 15)->update(
            ['http_path' => "/company/set/aboutus\r\n/company/set/aboutus/*\r\n/company/set/product\r\n/company/set/product/*\r\n/company/set/qa\r\n/company/set/qa/*\r\n/company/set/others\r\n/company/set/others/*", 'updated_at' => $now_date]
        );
        DB::table('admin_permissions')->where('id', 16)->update(
            ['http_path' => "/company/set/news\r\n/company/set/news/*", 'updated_at' => $now_date]
        );
        DB::table('admin_permissions')->where('id', 17)->update(
            ['http_path' => "/company/set/carousel\r\n/company/set/carousel/*", 'updated_at' => $now_date]
        );
        DB::table('admin_permissions')->where('id', 19)->update(
            ['http_path' => "/store/orders\r\n/store/orders_detail/*", 'updated_at' => $now_date]
        );
    }
}
