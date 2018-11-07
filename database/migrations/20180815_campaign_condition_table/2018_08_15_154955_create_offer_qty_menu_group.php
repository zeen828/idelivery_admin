<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferQtyMenuGroup extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_qty_menu_group', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->text('menu_item_ids')->nullable()->comment('menu_item的編號');
            $table->decimal('price', 8, 2)->default(0)->comment('多少錢');
            $table->integer('value')->default(0)->comment('優惠值(數量)');
            $table->integer('max_value')->default(1)->comment('優惠值上限(數量)');
        });
        DB::statement("ALTER TABLE `offer_qty_menu_group` comment '那些商品最低價幾件免費'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_qty_menu_group');
    }
}
