<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConditionMenuItem extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('condition_menu_item', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->text('menu_item_ids')->nullable()->comment('menu_item的編號');
            $table->decimal('value', 8, 2)->default(0)->comment('條件值');
        });
        DB::statement("ALTER TABLE `condition_menu_item` comment '活動條件設定檔(指定商品)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('condition_menu_item');
    }
}
