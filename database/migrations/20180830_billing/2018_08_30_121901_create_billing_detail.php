<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBillingDetail extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('admin.database.connection') ?: config('database.system_billing');

        Schema::connection($connection)->create('billing_detail', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('billing_id')->default(0)->comment('結帳單編號');
            $table->integer('sequence_no')->default(0)->comment('餐點明細排列序號');
            $table->integer('purchaser_id')->nullable()->comment('購買人會員編號');
            $table->string('purchaser_name', 20)->nullable()->comment('購買人名稱');
            $table->integer('operator_admin_id')->default(0)->comment('操作人員編號');
            $table->integer('item_id')->default(0)->comment('餐點編號 (0表折扣)');
            $table->string('item_name', 50)->nullable()->comment('餐點名稱');
            $table->text('base_price')->nullable()->comment('餐點基準價格(只記錄選定之 id, title, price)');
            $table->text('option')->nullable()->comment('餐點附加選項(只記錄選定之 unit_id, unit_title, attribute_id, attribute_title, extra_price)');
            $table->decimal('item_price', 8, 0)->default(0)->comment('單價');
            $table->integer('qty')->default(0)->comment('數量');
            $table->decimal('sub_price', 10, 0)->default(0)->comment('金額小計');
            $table->timestamps();
        });

        DB::connection($connection)->statement("ALTER TABLE `billing_detail` comment '結帳單明細'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing_detail');
    }
}
