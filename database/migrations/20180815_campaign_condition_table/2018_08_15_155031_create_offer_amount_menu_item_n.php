<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOfferAmountMenuItemN extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('offer_amount_menu_item_n', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('setting_id')->default(0)->comment('設定檔編號');
            $table->text('menu_item_ids')->nullable()->comment('menu_item的編號');
            $table->tinyInteger('n_th')->default(0)->comment('第幾件折抵');
            $table->decimal('value', 8, 2)->default(0)->comment('優惠值($)');
            $table->integer('max_value')->default(1)->comment('優惠值上限(數量)');

        });
        DB::statement("ALTER TABLE `offer_amount_menu_item_n` comment '那些商品同品項第幾件金額折扣多少錢'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('offer_amount_menu_item_n');
    }
}
