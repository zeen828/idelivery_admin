<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStoreTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 建立table
        Schema::create('store', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('company_id')->comment('公司ID');
            $table->integer('head_store_id')->nullable()->comment('總公司店家ID');
            $table->integer('area_id')->nullable()->comment('公司分區編號');
            $table->string('business_registration', 50)->comment('營利事業登記名稱');
            $table->string('uniform_numbers', 20)->nullable()->comment('營利事業登記編號');
            $table->string('name', 20)->nullable()->comment('名稱');
            $table->integer('district_id')->nullable()->comment('區域ID');
            $table->integer('city_id')->nullable()->comment('城市ID');
            $table->string('post_code', 5)->nullable()->comment('郵遞區號3碼或5碼');
            $table->string('district_name', 20)->nullable()->comment('區域');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->decimal('latitude', 10, 8)->nullable()->comment('緯度');
            $table->decimal('longitude', 11, 8)->nullable()->comment('經度');
            $table->string('order_phone', 20)->nullable()->comment('訂購電話號碼');
            $table->string('order_fax', 20)->nullable()->comment('訂購傳真號碼');
            $table->string('order_mobile_phone', 20)->nullable()->comment('訂購行動電話');
            $table->string('image', 255)->nullable()->comment('形象圖路徑');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態;1=正常;2=關閉');
            $table->text('description')->nullable()->comment('描述');
            $table->string('supervisor_name', 20)->nullable()->comment('店長姓名');
            $table->string('supervisor_phone', 20)->nullable()->comment('店長聯絡手機號碼');
            $table->string('supervisor_email', 255)->nullable()->comment('店長聯絡信箱');
            $table->enum('is_cooperation', ['1', '2'])->default('1')->comment('是否合作');
            $table->text('off_date')->nullable()->comment('公休日期');
            $table->text('business_hours')->nullable()->comment('營業時間');
            $table->text('order_hours')->nullable()->comment('接單時間');
            $table->text('carry_out_conditions')->nullable()->comment('外帶條件');
            $table->enum('allow_order_delivery', ['1', '2'])->default('1')->comment('允許外送;1=有外送;2=沒有外送');
            $table->text('delivery_conditions')->nullable()->comment('外送條件');
            $table->integer('delivery_interval_quota')->nullable()->comment('外送時段額度');
            $table->string('order_flow', 50)->nullable()->comment('訂單狀態流程');
            $table->softDeletes()->comment('軟刪除');
            $table->timestamp('create_time')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `store` comment '商店資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 刪除table
        Schema::dropIfExists('store');
    }
}
