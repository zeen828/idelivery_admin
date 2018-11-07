<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateReportOrder extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path="database/migrations/20181009_report_order"
     * php artisan migrate:rollback --path="database/migrations/20181009_report_order"
     * @return void
     */
    public function up()
    {
        Schema::table('report_order', function (Blueprint $table)
        {
            $table->integer('cancel_amount')->default(0)->comment('作廢金額')->after('amount');
            $table->integer('cancel_count')->default(0)->comment('作廢數量')->after('cancel_amount');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('report_order', function (Blueprint $table)
        {
            $table->dropColumn('cancel_amount');// 刪除欄位
            $table->dropColumn('cancel_count');// 刪除欄位
        });
    }
}
