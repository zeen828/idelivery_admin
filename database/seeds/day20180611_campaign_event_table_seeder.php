<?php

use Illuminate\Database\Seeder;

class day20180611_campaign_event_table_seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // 指令::php artisan db:seed --class=day20180611_campaign_event_table_seeder
        // 第一次執行,執行前全部資料庫清空
        $now_date = date('Y-m-d h:i:s');
        // 角色
        DB::table('campaign_event')->truncate();
        DB::table('campaign_event')->insert([
            // system
            ['id' => '1', 'keyword' => 'reg', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '2', 'keyword' => 'order', 'created_at' => $now_date, 'updated_at' => $now_date],
            ['id' => '3', 'keyword' => 'complete', 'created_at' => $now_date, 'updated_at' => $now_date],
        ]);
    }
}
