<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CarateAdminNotice extends Migration
{
    /**
     * Run the migrations.
     * php artisan migrate --path="database/migrations/20181009_notice"
     * php artisan migrate:rollback --path="database/migrations/20181009_notice"
     * @return void
     */
    public function up()
    {
        //
        $connection = config('admin.database.connection') ?: config('database.default');

        Schema::connection($connection)->create('admin_notice', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->tinyInteger('model')->default(0)->nullable()->comment('類型(0:一般訊息,1:系統通知,2:更新訊息)');
            $table->integer('company_id')->default(0)->comment('品牌ID(0:全公告,n:品牌公告)');
            $table->integer('store_id')->default(0)->comment('店家ID(0:全店家,n:指定店家)');
            $table->string('title', 255)->nullable()->comment('標題');
            $table->text('desc')->nullable()->comment('詳細內容');
            $table->string('url', 255)->nullable()->comment('連結');
            $table->timestamp('start_at')->nullable()->comment('起始時間');
            $table->timestamp('end_at')->nullable()->comment('結束時間');
            $table->tinyInteger('status')->default(1)->comment('狀態(0:停止;1:啟用)');
            $table->softDeletes()->comment('軟刪除');
            $table->timestamps();
            // index
            $table->index(['model', 'company_id', 'store_id', 'start_at', 'end_at', 'deleted_at'], 'notice_index');// 活動查詢用
        });

        DB::connection($connection)->statement("ALTER TABLE `admin_notice` comment '系統公告欄'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        $connection = config('admin.database.connection') ?: config('database.default');

        Schema::connection($connection)->dropIfExists('admin_notice');
    }
}
