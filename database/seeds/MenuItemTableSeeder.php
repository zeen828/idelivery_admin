<?php

use Illuminate\Database\Seeder;

class MenuItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=MenuItemTableSeeder
        $now_date = date('Y-m-d h:i:s');
        // 菜單-營業類別
        // DB::table('cuisine_category')->truncate();
        // 菜單-營業型態
        // DB::table('cuisine_type')->truncate();
        // 菜單-餐點分類群組
        // DB::table('cuisine_group')->truncate();
        // 菜單-餐點分類群組營業類別關聯
        // DB::table('cuisine_group_category')->truncate();
        // 菜單-餐點分類群組營業型態關聯
        // DB::table('cuisine_group_type')->truncate();
        // 菜單-附加選項
        // DB::table('cuisine_unit')->truncate();
        // 菜單-餐點分類附加選項關聯
        // DB::table('cuisine_unit_group')->truncate();
        // 菜單-附加選項細項
        // DB::table('cuisine_attr')->truncate();
        // 菜單-
        // DB::table('menu_item')->truncate();
        // 菜單-
        // DB::table('menu_item_unit')->truncate();
        // 菜單-
        // DB::table('menu_size')->truncate();
        // 菜單-
        // DB::table('menu_store_item')->truncate();
    }
}
