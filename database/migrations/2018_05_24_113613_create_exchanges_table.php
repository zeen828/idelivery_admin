<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateExchangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('exchanges', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('service_id')->comment('服務編號');
            $table->integer('company_id')->comment('品牌編號');
            $table->integer('store_id')->default(0)->comment('店家編號');
            $table->timestamp('start_date')->nullable()->comment('兌換起始日期');
            $table->timestamp('end_date')->nullable()->comment('兌換結束日期');
            $table->string('name', 50)->comment('商品名稱');
            $table->string('description', 100)->comment('商品說明');
            $table->string('image', 200)->nullable()->comment('商品圖片名稱');
            $table->integer('point_type_id')->comment('點數類型編號');
            $table->integer('point')->comment('兌換點數');
            $table->integer('stock')->default(0)->comment('目前存量');
            $table->integer('status')->default(1)->comment('上架否 (1: 上架; 2: 下架)');
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
        Schema::dropIfExists('exchanges');
    }
}
