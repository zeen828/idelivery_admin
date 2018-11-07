<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInvoiceLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->tinyInteger('third_type_id')->default(1)->comment('第三方平台編號(1:新電科技)');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->date('exec_date')->nullable()->comment('統計/查詢日期(Y-m-d)');
            $table->integer('orders_counts')->default(0)->comment('訂單筆數');
            $table->integer('invoice_counts')->default(0)->comment('發票數');
            $table->integer('upload_counts')->default(0)->comment('發票上傳筆數');
            $table->integer('callback_null_counts')->default(0)->comment('回傳null筆數');
            $table->integer('callback_counts')->default(0)->comment('第三方平台現有/回傳筆數');
            $table->tinyInteger('status')->default(0)->comment('狀態(1:開立;2:作廢)');
            $table->timestamps();

            $table->index(['exec_date', 'invoice_counts', 'callback_counts', 'status'], 'statistic_index');//查詢用
        });
        DB::statement("ALTER TABLE `invoice_log` comment '電子發票上傳日誌檔'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_log');
    }
}
