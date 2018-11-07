<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampaignSettingForDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('campaign_setting', function (Blueprint $table)
        {
            $table->tinyInteger('is_default')->default(0)->comment('是否預設')->after('sort_by');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('campaign_setting', function (Blueprint $table)
        {
            $table->dropColumn('is_default');// 刪除欄位
        });
    }
}
