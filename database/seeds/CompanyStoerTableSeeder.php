<?php

use Illuminate\Database\Seeder;

class CompanyStoerTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=CompanyStoerTableSeeder
        $now_date = date('Y-m-d h:i:s');
        // 品牌
        // DB::table('company')->truncate();
        // 店家
        // DB::table('store')->truncate();
    }
}
