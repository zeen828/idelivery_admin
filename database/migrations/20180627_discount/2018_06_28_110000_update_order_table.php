<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->integer('coupon_discount')->nullable()->comment('優惠券折扣')->after('src_amount');
            $table->integer('coupon_amount')->nullable()->comment('優惠券折扣後金額')->after('coupon_discount');
            $table->integer('custom_discount')->nullable()->comment('手動折扣')->after('coupon_amount');
            $table->integer('custom_amount')->nullable()->comment('手動折扣後金額')->after('custom_discount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order', function (Blueprint $table) {
            $table->dropColumn('coupon_discount');
            $table->dropColumn('coupon_amount');
            $table->dropColumn('custom_discount');
            $table->dropColumn('custom_amout');
        });
    }
}
