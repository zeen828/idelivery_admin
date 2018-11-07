<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBilling extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('admin.database.connection') ?: config('database.system_billing');

        Schema::connection($connection)->create('billing', function (Blueprint $table) {
            $table->increments('id');
            $table->string('pos_billing_id', 50)->comment('POS機結帳單編號');
            $table->integer('service_id')->default(0)->comment('服務編號');
            $table->integer('company_id')->default(0)->comment('品牌編號');
            $table->string('company_name', 50)->nullable()->comment('品牌名稱');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->string('store_name', 50)->nullable()->comment('店家名稱');
            $table->integer('member_id')->default(0)->comment('會員編號');
            $table->string('member_name', 20)->nullable()->comment('訂購人名稱(不是會員)');
            $table->string('purchase_phone', 20)->nullable()->comment('購買人聯絡電話');
            $table->tinyInteger('product_delivery')->default(1)->comment('取餐/貨方式(1:外送;2:外帶;3:內用)');
            $table->integer('total_qty')->default(0)->comment('總數量');
            $table->decimal('amount_src', 10, 0)->default(0)->comment('原價');
            $table->decimal('amount_campaign', 10, 0)->default(0)->comment('活動折扣金額');
            $table->decimal('amount_coupon', 10, 0)->default(0)->comment('優惠券折扣金額');
            $table->decimal('amount_custom', 10, 0)->default(0)->comment('手動折扣金額');
            $table->decimal('amount', 10, 0)->default(0)->comment('應付金額');
            $table->string('delivery_post_code', 5)->nullable()->comment('外送郵遞區號');
            $table->string('delivery_address', 50)->nullable()->comment('外送地址');
            $table->string('delivery_lng', 30)->nullable()->comment('外送地址經度');
            $table->string('delivery_lat', 30)->nullable()->comment('外送地址緯度');
            $table->string('prefer_datetime', 50)->nullable()->comment('希望外送/外帶時間(Y-m-d H:i)');
            $table->string('comment', 100)->nullable()->comment('結帳單備註');
            $table->date('billing_day')->nullable()->comment('開帳日期');
            $table->string('menu_version', 50)->nullable()->comment('菜單版本');
            $table->tinyInteger('status')->default(1)->comment('結帳單狀態(0:作廢;1:成立)');
//            $table->timestamp('created_at')->nullable()->comment('結帳/建立時間(預付/訂金)');
//            $table->timestamp('updated_at')->nullable()->comment('修改時間');
            $table->timestamp('completed_at')->nullable()->comment('完成時間(預設=建立時間)');
//            $table->timestamp('deleted_at')->nullable()->comment('作廢時間');
            $table->timestamps();
            $table->softDeletes();
            //唯一
            $table->unique('pos_billing_id', 'unique_pos_billing_id');
        });

        DB::connection($connection)->statement("ALTER TABLE `billing` comment '結帳單'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('billing');
    }
}
