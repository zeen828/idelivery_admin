<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateEInvoice extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('invoice_track', function (Blueprint $table)
        {
            $table->integer('upload_third')->default(0)->comment('上傳第三方狀態(0:未上傳, 1:上傳成功; 2:上傳失敗)')->after('last_invoice_date');
            $table->text('upload_data')->nullable()->comment('上傳第三方資料')->after('upload_third');
            $table->text('upload_time')->nullable()->comment('上傳第三方時間')->after('upload_data');
            $table->text('callback_message')->nullable()->comment('第三方回傳訊息')->after('upload_time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('invoice_track', function (Blueprint $table)
        {
            $table->dropColumn('upload_third');// 刪除欄位
            $table->dropColumn('upload_data');// 刪除欄位
            $table->dropColumn('upload_time');// 刪除欄位
            $table->dropColumn('callback_message');// 刪除欄位
        });
    }
}
