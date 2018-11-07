<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuItemVersionLog extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('publish_version_log', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('store_id')->default(0)->comment('店家ID');
            $table->tinyInteger('types')->default(0)->comment('記錄類型1=菜單;2=行銷活動;3=子母畫面');
            $table->text('version')->nullable()->comment('版本號');
            $table->text('file_url')->nullable()->comment('檔案URL位置');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `publish_version_log` comment '發佈版本號紀錄'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('publish_version_log');
    }
}
