<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOfferQty extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_qty', function (Blueprint $table)
        {
            $table->decimal('price', 8, 2)->default(0)->comment('多少錢')->after('setting_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_qty', function (Blueprint $table)
        {
            $table->dropColumn('price');// 刪除欄位
        });
    }
}
