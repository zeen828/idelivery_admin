<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateCuisineGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cuisine_group', function (Blueprint $table)
        {
            $table->tinyInteger('hidden')->default(0)->comment('顯示狀態(0:顯示,1:隱藏)')->after('group_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cuisine_group', function (Blueprint $table)
        {
            $table->dropColumn('hidden');// 刪除欄位
        });
    }
}
