<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBackendTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 建立table
        Schema::create('log', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->enum('type', ['1', '2', '3', '4'])->default('1')->comment('紀錄類型:1=管理者操作;2=會員操作;3=管理者登入;4=會員登入');
            $table->enum('use', ['1', '2', '3'])->comment('操作動作:1=新增;2=修改;3=刪除');
            $table->integer('key')->nullable()->comment('新增的id或是更新的id');
            $table->string('table', 100)->comment('操作的資料表');
            $table->string('account', 255)->comment('紀錄帳號');
            $table->text('message')->comment('紀錄訊息');
            $table->string('browser', 255)->comment('瀏覽器資訊');
            $table->integer('ip')->nullable()->comment('操作的IP位置');
            $table->timestamp('create_time')->nullable()->comment('紀錄建立時間');
        });
        DB::statement("ALTER TABLE `log` comment 'log紀錄'");

        Schema::create('member', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('ID');
            $table->string('uuid', 64)->comment('會員識別碼');
            $table->enum('status', ['1', '2', '3'])->default('1')->comment('會員狀態:1=正常;2=關閉;3=刪除');
            $table->enum('is_lock', ['1', '2', '3'])->default('1')->comment('鎖定狀態:1=未鎖定;2=鎖定');
            $table->integer('company_id')->comment('所屬品牌編號');
            $table->enum('is_valid', ['1', '2'])->default('1')->comment('簡訊驗證:1=未通過;2=已通過');
            $table->string('captcha', 4)->comment('手機驗證碼');
            $table->string('login_token', 70)->nullable()->comment('登入狀態');
            $table->string('country', 10)->default('+886')->comment('電話號碼國碼');
            $table->string('account', 255)->comment('帳號');
            $table->string('password', 60)->comment('密碼');
            $table->string('name', 255)->nullable()->comment('姓名');
            $table->string('email', 255)->nullable()->comment('電子信箱');
            $table->string('post_code', 6)->nullable()->comment('郵遞區號');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->string('contact_phone', 15)->nullable()->comment('聯絡電話');
            $table->string('facebook_id', 50)->nullable()->comment('第三方登入=facebook');
            $table->string('line_id', 50)->nullable()->comment('第三方登入=line');
            $table->text('personal_img')->nullable()->comment('會員頭像');
            $table->tinyInteger('login_error_num')->default(0)->comment('登入錯誤次數');
            $table->tinyInteger('os')->default(0)->comment('手機作業系統:0=無,1=ios,2=Android');
            $table->decimal('app_version', 5, 1)->nullable()->comment('會員app版本');
            $table->string('token', 255)->nullable()->comment('推撥token');
            $table->timestamp('last_login_time')->nullable()->comment('最近登入時間');
            $table->timestamp('create_time')->nullable()->comment('建立時間');
        });
        DB::statement("ALTER TABLE `member` comment '會員資料'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 刪除table
        Schema::dropIfExists('log');
        Schema::dropIfExists('member');
//        Schema::dropIfExists('news');
//        Schema::dropIfExists('question_answer');
//        Schema::dropIfExists('setting');
//        Schema::dropIfExists('district_tw');
//
//        Schema::dropIfExists('company_store_business_hours');
//        Schema::dropIfExists('store_delivery_condition');
    }
}
