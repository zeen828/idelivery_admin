<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateBilling extends Migration
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
            $table->string('cancel_reason', 100)->nullable()->comment('作廢原因')->after('status');
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
            $table->dropColumn('cancel_reason');// 刪除欄位
        });
    }
}
