<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEInvoiceTabe extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('invoice', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('store_id')->comment('店家編號');
            $table->integer('order_id')->comment('訂單編號');
            $table->string('invoice_no', 10)->comment('發票號碼');
            $table->string('invoice_date', 8)->comment('發票開立日期');
            $table->time('invoice_time')->comment('發票開立時間');
            $table->string('seller_id', 10)->comment('賣方營利事業統一編號');
            $table->string('seller_name', 60)->comment('賣方營業人名稱');
            $table->string('seller_address', 100)->nullable()->comment('賣方地址');
            $table->string('seller_person_in_charge', 30)->nullable()->comment('賣方負責人姓名');
            $table->string('seller_telephone_no', 26)->nullable()->comment('賣方電話號碼');
            $table->string('seller_facsimile_no', 26)->nullable()->comment('賣方傳真號碼');
            $table->string('seller_email', 80)->nullable()->comment('賣方電子郵件地址');
            $table->string('seller_customer_no', 20)->nullable()->comment('賣方客戶編號');
            $table->string('seller_role_remark', 40)->nullable()->comment('賣方營業人角色註記');
            $table->string('buyer_id', 10)->nullable()->comment('買方營利事業統一編號');
            $table->string('buyer_name', 60)->nullable()->comment('B2B-營業人名稱/B2C-業者通知消費者之個人識別碼資料');
            $table->string('buyer_address', 100)->nullable()->comment('買方地址');
            $table->string('buyer_person_in_charge', 30)->nullable()->comment('買方負責人姓名');
            $table->string('buyer_telephone_no', 26)->nullable()->comment('買方電話號碼');
            $table->string('buyer_facsimile_no', 26)->nullable()->comment('買方傳真號碼');
            $table->string('buyer_email', 80)->nullable()->comment('買方電子郵件地址');
            $table->string('buyer_customer_no', 20)->nullable()->comment('買方客戶編號');
            $table->string('buyer_role_remark', 40)->nullable()->comment('買方營業人角色註記');
            $table->string('check_no', 10)->nullable()->comment('發票檢查碼');
            $table->string('buyer_remark', 1)->nullable()->comment('買受人註記欄列表');
            $table->string('main_remark', 200)->nullable()->comment('總備註');
            $table->string('customs_clearance_mark', 1)->nullable()->comment('通關方式列表');
            $table->string('category', 2)->nullable()->comment('沖帳別');
            $table->string('relate_no', 20)->nullable()->comment('相關號碼');
            $table->string('invoice_type', 2)->comment('發票類別');
            $table->string('group_mark', 1)->nullable()->comment('彙開註記');
            $table->string('carrier_id', 30)->nullable()->comment('載具號碼');
            $table->string('donate_mark', 7)->nullable()->comment('捐贈註記(註記愛心碼)');
            $table->string('print_mark', 1)->default("N")->comment('紙本電子發票已列印註記(Y：需列印;N：不需列印)');
            $table->binary('attachment')->nullable()->comment('通關方式列表');
            $table->bigInteger('sales_amount')->comment('銷售額合計');
            $table->bigInteger('free_tax_amount')->comment('免稅金額');
            $table->bigInteger('zero_tax_amount')->comment('零稅金額');
            $table->string('tax_type', 1)->comment('課稅別');
            $table->decimal('tax_rate', 8, 2)->comment('稅率');
            $table->bigInteger('tax_amount')->comment('營業稅額');
            $table->bigInteger('total_amount')->comment('總計');
            $table->string('order_info', 50)->nullable()->comment('備註(自行定義，列印發票時將會顯示在明細下方，若為信用卡消費，則需多備註卡號後四碼)');
            $table->bigInteger('discount_amount')->nullable()->comment('折扣金額');
            $table->decimal('original_currency_amount', 8, 2)->nullable()->comment('原幣金額');
            $table->decimal('exchange_rate', 8, 2)->nullable()->comment('匯率');
            $table->string('currency', 3)->nullable()->comment('幣別');
            $table->integer('upload_third')->default(0)->comment('上傳第三方平台狀態(0:未上傳;1:已上傳');
            $table->string('message_third', 100)->nullable()->comment('第三方平台回傳訊息');
            $table->timestamp('upload_third_datetime')->nullable()->comment('上傳第三方平台時間');
            $table->timestamp('callback_third_datetime')->nullable()->comment('第三方平台回傳時間');
            $table->integer('upload')->default(0)->comment('上傳財政部狀態(0:未上傳;1:已上傳');
            $table->string('message', 100)->nullable()->comment('財政部回傳訊息');
            $table->timestamp('upload_datetime')->nullable()->comment('上傳財政部時間');
            $table->timestamp('callback_datetime')->nullable()->comment('財政部回傳時間');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `invoice` comment '電子發票主資料檔'");

        Schema::create('invoice_detail', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('invoice_id')->comment('電子發票主資料檔編號');
            $table->string('description', 256)->comment('品名');
            $table->decimal('quantity', 8, 2)->comment('數量');
            $table->string('unit', 6)->nullable()->comment('單位');
            $table->decimal('unit_price', 8, 2)->comment('單價');
            $table->decimal('amount', 8, 2)->comment('金額');
            $table->string('sequence_no', 3)->comment('發票明細之排列序號');
            $table->string('remark', 40)->nullable()->comment('單一欄位備註');
            $table->string('relate_no', 20)->nullable()->comment('相關號碼');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `invoice_detail` comment '電子發票明細資料檔'");

//        Schema::create('invoice_confirm', function (Blueprint $table) {
//            $table->increments('id')->comment('ID');
//            $table->string('invoice_no', 10)->comment('發票號碼');
//            $table->string('invoice_date', 8)->comment('發票開立日期');
//            $table->string('buyer_id', 10)->comment('買方統一編號');
//            $table->string('seller_id', 10)->comment('賣方統一編號');
//            $table->string('receive_date', 8)->comment('發票接收日期');
//            $table->time('receive_time')->comment('發票接收時間');
//            $table->string('buyer_remark', 1)->nullable()->comment('買受人註記欄列表');
//            $table->string('remark', 200)->nullable()->comment('備註');
//            $table->string('bonded_area_confirm', 1)->nullable()->comment('買受人簽署適用零稅率註記');
//            $table->timestamp('created_at')->nullable()->comment('建立時間');
//            $table->timestamp('updated_at')->nullable()->comment('更新時間');
//        });
//        DB::statement("ALTER TABLE `invoice_confirm` comment '發票接收確認資料檔'");

        Schema::create('invoice_track', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('store_id')->comment('店家號碼');
            $table->string('period_year', 4)->comment('發票期別年份(民國)');
            $table->string('period_month', 5)->comment('發票期別月份');
            $table->string('track_en', 2)->comment('發票字軌英文');
            $table->string('start_no', 8)->comment('發票起始號碼');
            $table->string('end_no', 8)->comment('發票結束號碼');
            $table->integer('counts')->comment('發票號碼總數');
            $table->integer('remain_counts')->comment('發票號碼剩餘筆數');
            $table->integer('current_no')->nullable()->comment('發票目前號碼');
            $table->timestamp('last_invoice_date')->nullable()->comment('最後發票日期');
            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `invoice_track` comment '發票字軌資料檔'");


        Schema::create('invoice_cancel', function (Blueprint $table) {
            $table->increments('id')->comment('ID');
            $table->integer('store_id')->comment('店家編號');
            $table->string('invoice_no', 10)->comment('發票號碼');
            $table->string('invoice_date', 8)->comment('發票開立日期');
            $table->string('buyer_id', 10)->nullable()->comment('買方統一編號');
            $table->string('seller_id', 10)->comment('賣方統一編號');
            $table->string('cancel_date', 8)->comment('發票作廢日期');
            $table->time('cancel_time')->comment('發票作廢時間');
            $table->string('cancel_reason', 20)->comment('作廢原因');
            $table->string('return_tax_document_no', 60)->nullable()->comment('專案作廢核准文號');
            $table->string('remark', 200)->nullable()->comment('備註');
            $table->integer('upload_third')->default(0)->comment('上傳第三方平台狀態(0:未上傳;1:已上傳');
            $table->string('message_third', 100)->nullable()->comment('第三方平台回傳訊息');
            $table->timestamp('upload_third_datetime')->nullable()->comment('上傳第三方平台時間');
            $table->timestamp('callback_third_datetime')->nullable()->comment('第三方平台回傳時間');
            $table->integer('upload')->default(0)->comment('上傳財政部狀態(0:未上傳;1:已上傳');
            $table->string('message', 100)->nullable()->comment('財政部回傳訊息');
            $table->timestamp('upload_datetime')->nullable()->comment('上傳財政部時間');
            $table->timestamp('callback_datetime')->nullable()->comment('財政部回傳時間');            $table->timestamp('created_at')->nullable()->comment('建立時間');
            $table->timestamp('updated_at')->nullable()->comment('更新時間');
        });
        DB::statement("ALTER TABLE `invoice_cancel` comment '發票接收確認資料檔'");

//        Schema::create('invoice_cancel_confirm', function (Blueprint $table) {
//            $table->increments('id')->comment('ID');
//            $table->string('invoice_no', 10)->comment('發票號碼');
//            $table->string('invoice_date', 8)->comment('發票開立日期');
//            $table->string('buyer_id', 10)->comment('買方統一編號');
//            $table->string('seller_id', 10)->comment('賣方統一編號');
//            $table->string('cancel_date', 8)->comment('發票作廢日期');
//            $table->time('cancel_time')->comment('發票作廢時間');
//            $table->string('remark', 200)->nullable()->comment('備註');
//            $table->timestamp('created_at')->nullable()->comment('建立時間');
//            $table->timestamp('updated_at')->nullable()->comment('更新時間');
//        });
//        DB::statement("ALTER TABLE `invoice_cancel_confirm` comment '發票接收確認資料檔'");


//        Schema::create('allowance', function (Blueprint $table) {
//            $table->increments('id')->comment('ID');
//            $table->string('allowance_no', 16)->comment('折讓證明單號碼');
//            $table->string('allowance_date', 8)->comment('折讓證明單開立日期');
//            $table->string('seller_id', 10)->comment('賣方營利事業統一編號');
//            $table->string('seller_name', 60)->comment('賣方營業人名稱');
//            $table->string('seller_address', 100)->nullable()->comment('賣方地址');
//            $table->string('seller_person_in_charge', 30)->nullable()->comment('賣方負責人姓名');
//            $table->string('seller_telephone_no', 26)->nullable()->comment('賣方電話號碼');
//            $table->string('seller_facsimile_no', 26)->nullable()->comment('賣方傳真號碼');
//            $table->string('seller_email', 80)->nullable()->comment('賣方電子郵件地址');
//            $table->string('seller_customer_no', 20)->nullable()->comment('賣方客戶編號');
//            $table->string('seller_role_remark', 40)->nullable()->comment('賣方營業人角色註記');
//            $table->string('buyer_id', 10)->comment('買方營利事業統一編號');
//            $table->string('buyer_name', 60)->comment('B2B-營業人名稱/B2C-業者通知消費者之個人識別碼資料');
//            $table->string('buyer_address', 100)->nullable()->comment('買方地址');
//            $table->string('buyer_person_in_charge', 30)->nullable()->comment('買方負責人姓名');
//            $table->string('buyer_telephone_no', 26)->nullable()->comment('買方電話號碼');
//            $table->string('buyer_facsimile_no', 26)->nullable()->comment('買方傳真號碼');
//            $table->string('buyer_email', 80)->nullable()->comment('買方電子郵件地址');
//            $table->string('buyer_customer_no', 20)->nullable()->comment('買方客戶編號');
//            $table->string('buyer_role_remark', 40)->nullable()->comment('買方營業人角色註記');
//            $table->string('allowance_type', 1)->comment('折讓種類');
//            $table->binary('attachment')->nullable()->comment('證明附件');
//            $table->timestamp('created_at')->nullable()->comment('建立時間');
//            $table->timestamp('updated_at')->nullable()->comment('更新時間');
//        });
//        DB::statement("ALTER TABLE `allowance` comment '開立折讓證明單|傳送折讓證明單通知'");


    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // 刪除table
        Schema::dropIfExists('invoice');
        Schema::dropIfExists('invoice_detail');
//        Schema::dropIfExists('invoice_confirm');
        Schema::dropIfExists('invoice_track');
        Schema::dropIfExists('invoice_cancel');
//        Schema::dropIfExists('invoice_cancel_confirm');
    }
}
