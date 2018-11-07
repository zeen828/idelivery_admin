<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProductExchangeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_exchange', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('service_id')->comment('服務編號');
            $table->integer('member_id')->default(0)->comment('非正式會員編號');
            $table->integer('company_id')->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->integer('member_detail_id')->comment('正式會員編號');
            $table->timestamp('date')->nullable()->comment('兌換日期');
            $table->integer('exchanges_id')->comment('兌換商品編號');
            $table->integer('qty')->comment('兌換數量');
            $table->integer('point_type_id')->comment('點數類型編號');
            $table->integer('point_before')->comment('兌換前點數');
            $table->integer('point_after')->comment('兌換後點數');
            $table->integer('orders_id')->comment('訂單編號');
            $table->integer('status')->default(0)->comment('兌換成功否 (1: 成功; 0: 失敗)');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_exchange');
    }
}
