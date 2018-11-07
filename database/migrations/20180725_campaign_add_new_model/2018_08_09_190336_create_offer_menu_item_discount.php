<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferMenuItemDiscount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_menu_item_discount', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->text('menu_item_ids')->nullable()->comment('menu_item的編號');
            $table->decimal('value', 8, 2)->default(0)->comment('優惠值(折數)');
            $table->decimal('max_value', 8, 2)->default(0)->comment('優惠值上限(數量)');
        });
        DB::statement("ALTER TABLE `offer_menu_item_discount` comment '活動優惠設定檔(指定商品折扣)'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_menu_item_discount');
    }
}
