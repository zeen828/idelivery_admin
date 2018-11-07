<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('store', function (Blueprint $table) {
            $table->tinyInteger('invoice_third_id')->default(0)->comment('電子發票第三方編號(1:新電)')->after('billing_day');
            $table->string('invoice_third_account', 20)->nullable()->comment('電子發票上傳帳號')->after('invoice_third_id');
            $table->string('invoice_third_password', 100)->nullable()->comment('電子發票密碼')->after('invoice_third_account');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('store', function (Blueprint $table) {
            $table->dropColumn('invoice_third_id');// 刪除欄位
            $table->dropColumn('invoice_third_account');// 刪除欄位
            $table->dropColumn('invoice_third_password');// 刪除欄位
        });
    }
}
