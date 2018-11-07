<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateMenuItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 更新table
        Schema::table('menu_item', function (Blueprint $table) {
            $table->dropColumn('intro');// 刪除欄位
            $table->dropColumn('picture');// 刪除欄位
            $table->dropColumn('spec_relation');// 刪除欄位
        });
        Schema::table('menu_item', function (Blueprint $table) {
            $table->text('intro')->nullable()->comment('簡介')->after('group_id');
            $table->string('picture', 100)->nullable()->comment('圖片')->after('intro');
            $table->text('spec_relation')->nullable()->comment('附加選項')->after('picture');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_item', function (Blueprint $table) {
            $table->dropColumn('intro');// 刪除欄位
            $table->dropColumn('picture');// 刪除欄位
            $table->dropColumn('spec_relation');// 刪除欄位
        });
        Schema::table('menu_item', function (Blueprint $table) {
            $table->text('intro')->comment('簡介')->after('group_id');
            $table->string('picture', 100)->comment('圖片')->after('intro');
            $table->text('spec_relation')->comment('附加選項')->after('picture');
        });
    }
}
