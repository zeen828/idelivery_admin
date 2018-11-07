<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBillingForMember extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('billing', function (Blueprint $table)
        {
            $table->integer('order_id')->default(0)->comment('訂單編號(App)')->after('pos_billing_id');
            $table->integer('member_info_log_id')->default(0)->comment('會員資訊日誌檔編號')->after('purchase_phone');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('billing', function (Blueprint $table)
        {
            $table->dropColumn('order_id');// 刪除欄位
            $table->dropColumn('member_info_log_id');// 刪除欄位
        });
    }
}
