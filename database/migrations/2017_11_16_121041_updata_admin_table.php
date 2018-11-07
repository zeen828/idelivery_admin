.<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdataAdminTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 指定連線
        $connection = config('admin.database.connection') ?: config('database.default');

        //補table說明
        DB::connection($connection)->statement("ALTER TABLE `admin_menu` comment '後台目錄'");
        DB::connection($connection)->statement("ALTER TABLE `admin_operation_log` comment '後台LOG紀錄'");
        DB::connection($connection)->statement("ALTER TABLE `admin_permissions` comment '後台權限'");
        DB::connection($connection)->statement("ALTER TABLE `admin_roles` comment '後台角色'");
        DB::connection($connection)->statement("ALTER TABLE `admin_role_menu` comment '後台角色目錄'");
        DB::connection($connection)->statement("ALTER TABLE `admin_role_permissions` comment '後台角色權限'");
        DB::connection($connection)->statement("ALTER TABLE `admin_role_users` comment '後台角色使用者'");
        DB::connection($connection)->statement("ALTER TABLE `admin_users` comment '後台使用者'");
        DB::connection($connection)->statement("ALTER TABLE `admin_user_permissions` comment '後台使用者權限'");

        // 更新table
        Schema::connection($connection)->table(config('admin.database.users_table'), function (Blueprint $table)
        {
            $table->integer('admin_user_id')->default(0)->comment('上線ID')->after('id');// 建立欄位(int型態)
            $table->string('captcha', 4)->nullable()->comment('認證碼')->after('remember_token');// 建立欄位(string型態)
            $table->softDeletes()->comment('軟刪除')->after('captcha');// 軟刪除
        });

        // 更新table
        Schema::connection($connection)->table(config('admin.database.roles_table'), function (Blueprint $table)
        {
            $table->integer('company_id')->default(0)->comment('公司ID')->after('id');// 建立欄位(int型態)
            $table->integer('store_id')->default(0)->comment('店家ID')->after('company_id');// 建立欄位(int型態)
            $table->integer('admin_role_id')->default(0)->comment('上線ID')->after('store_id');// 建立欄位(int型態)
            $table->string('title', 50)->comment('客戶端顯示文字')->after('name');// 建立欄位(string型態)
            $table->softDeletes()->comment('軟刪除')->after('slug');// 軟刪除
            //索引
            $table->index(['company_id', 'store_id']);
        });

        // 建立table
        Schema::connection($connection)->create('admin_user_store', function (Blueprint $table) {
            $table->integer('user_id')->comment('會員ID');
            $table->integer('store_id')->comment('店家ID');
            $table->timestamps();
            //索引
            $table->index(['user_id', 'store_id']);
        });
        DB::connection($connection)->statement("ALTER TABLE `admin_user_store` comment '後台使用者店家'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 指定連線
        $connection = config('admin.database.connection') ?: config('database.default');

        // 更新table
        Schema::connection($connection)->table(config('admin.database.users_table'), function (Blueprint $table)
        {
            $table->dropColumn('admin_user_id');// 刪除欄位
            $table->dropColumn('captcha');// 刪除欄位
            $table->dropSoftDeletes();// 軟刪除
        });

        // 更新table
        Schema::connection($connection)->table(config('admin.database.roles_table'), function (Blueprint $table)
        {
            $table->dropColumn('company_id');// 刪除欄位
            $table->dropColumn('store_id');// 刪除欄位
            $table->dropColumn('admin_role_id');// 刪除欄位
            $table->dropColumn('title');// 刪除欄位
            $table->dropSoftDeletes();// 軟刪除
        });

        // 刪除table
        Schema::connection($connection)->dropIfExists('admin_user_store');
    }
}
