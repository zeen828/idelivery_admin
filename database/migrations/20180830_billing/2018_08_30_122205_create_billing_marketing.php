<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingMarketing extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('admin.database.connection') ?: config('database.system_billing');

        Schema::connection($connection)->create('billing_marketing', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('billing_id')->default(0)->comment('結帳單編號');
            $table->integer('marketing_type')->default(0)->comment('促銷活動類型(1: 活動; 2:優惠券)');
            $table->string('marketing_sn', 50)->nullable()->comment('促銷活動設定/優惠券編號(base64_encode)');
            $table->integer('setting_id')->default(0)->comment('活動/優惠設定編號');
            $table->decimal('deduct_amount', 8, 2)->default(0)->comment('減免金額');
            $table->timestamps();
        });

        DB::connection($connection)->statement("ALTER TABLE `billing_marketing` comment '結帳單促銷活動檔'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_marketing');
    }
}
