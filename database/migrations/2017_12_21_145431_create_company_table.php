<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 建立table
        Schema::create('company', function (Blueprint $table) {
            $table->increments('id')->comment('公司ID');
            $table->string('brand', 50)->comment('公司品牌');
            $table->string('name', 50)->comment('公司名稱');
            $table->string('uniform_numbers', 20)->nullable()->comment('統編');
            $table->string('supervisor_name', 20)->nullable()->comment('主管名稱');
            $table->string('supervisor_phone', 20)->nullable()->comment('主管電話');
            $table->string('supervisor_email', 255)->nullable()->comment('主管信箱');
            $table->integer('district_id')->nullable()->comment('區域ID');
            $table->integer('city_id')->nullable()->comment('城市ID');
            $table->string('post_code', 5)->nullable()->comment('郵遞區號3碼或5碼');
            $table->string('district_name', 20)->nullable()->comment('區域');
            $table->string('address', 255)->nullable()->comment('地址');
            $table->string('image', 255)->nullable()->comment('形象圖路徑');
            $table->decimal('profit', 5, 2)->default(0)->comment('拆帳比');
            $table->string('bank', 20)->nullable()->comment('銀行');
            $table->string('bank_branch', 20)->nullable()->comment('銀行分行');
            $table->string('bank_account', 30)->nullable()->comment('銀行帳號');
            $table->string('passbook_picture', 255)->nullable()->comment('銀行存摺');
            $table->text('remarks')->nullable()->comment('備註');
            $table->enum('status', ['1', '2'])->default('1')->comment('公司狀態;1=正常;2=關閉');
            $table->softDeletes()->comment('軟刪除');
            $table->timestamp('create_time')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `company` comment '公司品牌資料表'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 刪除table
        Schema::dropIfExists('company');
    }
}
