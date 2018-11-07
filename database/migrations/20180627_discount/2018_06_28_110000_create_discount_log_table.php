<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDiscountLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('discount_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('order_id')->default(0)->comment('訂單ID');
            $table->integer('type')->nullable()->comment('金額類型, 0: 原始金額, 1: 活動金額, 2: 優惠券金額, 3: 人工折扣金額');
            $table->integer('discount')->nullable()->comment('折扣');
            $table->integer('amount')->nullable()->comment('折扣後金額');
            $table->timestamp('created_at')->nullable()->comment('建立時間');

            $table->index('order_id');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('discount_log');
    }
}
