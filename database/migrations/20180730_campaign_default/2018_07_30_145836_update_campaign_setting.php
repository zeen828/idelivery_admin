<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCampaignSetting extends Migration
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
            $table->integer('sort_by')->nullable()->comment('排序')->after('hidden');
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
            $table->dropColumn('sort_by');// 刪除欄位
        });
    }
}
