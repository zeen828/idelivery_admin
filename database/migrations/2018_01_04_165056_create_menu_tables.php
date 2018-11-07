<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenuTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cuisine_category', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('name', 50)->comment('名稱');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_category` comment '營業類別'");

        Schema::create('cuisine_type', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->string('name', 50)->comment('名稱');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_type` comment '營業型態'");

        Schema::create('cuisine_group_category', function (Blueprint $table) {
            $table->integer('group_id')->comment('餐點分類群組ID');
            $table->integer('category_id')->comment('營業類別ID');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_group_category` comment '餐點分類群組營業類別關聯'");

        Schema::create('cuisine_group_type', function (Blueprint $table) {
            $table->integer('group_id')->comment('餐點分類群組ID');
            $table->integer('type_id')->comment('營業型態ID');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_group_type` comment '餐點分類群組營業型態關聯'");

        Schema::create('cuisine_group', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('company_id')->comment('總部ID');
            $table->integer('store_id')->comment('點家ID(0總部擁有)');
            $table->string('group_name', 50)->comment('名稱');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_group` comment '餐點分類群組'");

        Schema::create('cuisine_unit_group', function (Blueprint $table) {
            $table->integer('unit_id')->comment('附加選項ID');
            $table->integer('group_id')->comment('餐點分類群組ID');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_unit_group` comment '餐點分類群組附加選項關聯'");

        Schema::create('cuisine_unit', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('company_id')->comment('總部ID');
            $table->integer('store_id')->comment('點家ID(0總部擁有)');
            $table->string('unit_name', 50)->comment('名稱');
            $table->enum('is_multiple', ['1', '2'])->default('2')->comment('多選');
            $table->enum('is_required', ['1', '2'])->default('2')->comment('必填');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_unit` comment '附加選項'");

        Schema::create('cuisine_attr', function (Blueprint $table) {
            $table->increments('id')->comment('');
            $table->integer('company_id')->comment('總部ID');
            $table->integer('store_id')->comment('點家ID(0總部擁有)');
            $table->integer('unit_id')->comment('附加選項ID');
            $table->string('attr_name', 50)->comment('細項名稱');
            $table->enum('is_default', ['1', '2'])->default('2')->comment('預設');
            $table->decimal('extra_price', 10, 0)->comment('額外的價格');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `cuisine_attr` comment '附加選項細項'");

        Schema::create('menu_item_unit', function (Blueprint $table) {
            $table->integer('item_id')->comment('餐點項目ID');
            $table->integer('unit_id')->comment('附加選項ID');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `menu_item_unit` comment '餐點分類附加選項關聯'");

        Schema::create('menu_item', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('company_id')->comment('總部ID');
            $table->integer('store_id')->comment('點家ID(0總部擁有))');
            $table->string('name', 50)->comment('餐點名稱');
            $table->integer('group_id')->comment('餐點分類群組ID');
            $table->text('intro')->comment('介绍');
            $table->string('picture', 100)->comment('圖片');
            $table->text('spec_relation')->comment('規格關係');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `menu_item` comment '餐點項目'");

        Schema::create('menu_size', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('item_id')->comment('餐點項目ID');
            $table->string('size_name', 50)->comment('尺寸');
            $table->decimal('price', 10, 0)->comment('價錢');
            $table->enum('is_selected', ['1', '2'])->default('2')->comment('預設選項');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `menu_size` comment '餐點項目尺寸價錢'");

        Schema::create('menu_store_item', function (Blueprint $table) {
            $table->integer('store_id')->comment('點家ID');
            $table->integer('item_id')->comment('餐點項目ID');
            $table->enum('status', ['1', '2'])->default('1')->comment('狀態');
            $table->integer('sort_by')->nullable()->comment('排序');
            $table->timestamps();
        });
        DB::statement("ALTER TABLE `menu_store_item` comment '菜單店家關聯'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('admin.database.connection') ?: config('database.default');

        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_category_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_type_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_group_category_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_group_type_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_group_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_unit_group_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_unit_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.cuisine_attr_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.menu_item_unit_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.menu_item_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.menu_size_table'));
        Schema::connection($connection)->dropIfExists(config('admin.database.menu_store_item_table'));
    }
}
