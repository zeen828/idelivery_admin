<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddExchangesTypeToExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table
                ->smallInteger('exchanges_type')
                ->comment('兌換商品類型(1:實體,2:優惠券)')
                ->after('point_type_id')
                ->default(1);
            $table
                ->unsignedInteger('campaign_setting_id')
                ->comment('優惠活動ID')
                ->after('exchanges_type')
                ->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exchanges', function (Blueprint $table) {
            $table->dropColumn('exchanges_type');
            $table->dropColumn('campaign_setting_id');
        });
    }
}
