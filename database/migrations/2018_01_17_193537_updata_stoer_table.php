<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdataStoerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        // 更新table
        Schema::table('store', function (Blueprint $table)
        {
            $table->text('intro_url')->nullable()->comment('店家網址')->after('description');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store', function (Blueprint $table)
        {
            $table->dropColumn('intro_url');// 刪除欄位
        });
    }
}
