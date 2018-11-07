<?php

use Illuminate\Routing\Router;
use Collective\Html\HtmlFacade;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

//1.系統功能
    $router->get('/', 'System\SystemController@autoLoadSession');//system-會員登入後前置功能
    $router->get('/dashboard', 'HomeController@index')->name('dashboard');//system-判斷群組顯示首頁資訊
    $router->match(['get', 'post'],'/system/change_config', 'HomeController@change_config_id');//system-切換店家
//2.商店管理
    $router->prefix('management')->group(function(Router $router){
        $router->get('/', 'HomeController@management_info');//大後台-首頁資料
        $router->resources([
                'set/company'                                => Management\CompanyController::class,//大後台-總部品牌設定
                'set/store'                                  => Management\StoreController::class,//大後台-總部總店設定
                'set/user'                                   => Management\UserController::class,//大後台-總部會員管理
                'set_company_store_user/{store_id}/user'     => Management\CompanyUserController::class,//大後台-總部總店設定-總部總店建立帳號
                'set/cuisine_category'                       => Management\CuisineCategoryController::class,//大後台-營業設定-營業類別
                'set/cuisine_type'                           => Management\CuisineTypeController::class,//大後台-營業設定-營業項目
                'set/app_config'                             => Management\AppConfigController::class,//大後台-品牌設定檔上傳
                'set/store_config'                           => Management\StoreConfigController::class,//大後台-店家設定檔上傳
                'sms_log'                                    => Management\SmsLogController::class,//大後台-簡訊紀錄
                'set/notice'                                 => Management\AdminNoticeController::class,//大後台-布告欄
        ]);
        $router->get('sms/status', 'Management\SmsLogController@status');//品牌管理-SMS 簡訊發送狀態查詢
        $router->get('sms/send', 'Management\SmsLogController@send');//品牌管理-SMS 簡訊重發
    });
    $router->post('/system/image_upload', 'System\ImageController@upload');
    $router->any('/system/config_upload', 'System\AppConfigController@upload');
    $router->any('/system/store_config_upload', 'System\StoreConfigController@upload');
//3.財務管理
//4.品牌管理
    $router->prefix('company')->group(function(Router $router){
        $router->get('/', 'HomeController@company_info');//總店-首頁資料

        $router->prefix('set')->group(function(Router $router){
            $router->resources([
                'store'         => Company\StoreController::class,//品牌管理>>店家設定
                'cuisine_group' => Company\CuisineGroupController::class,//品牌管理>>餐點管理>>餐點分類
                'menu_item'     => Company\MenuItemController::class,//品牌管理-餐點管理-餐點分類-編輯餐點(透過URL進入做編輯餐點)
                'cuisine_unit'  => Company\CuisineUnitController::class,//品牌管理-餐點管理-選項管理
                'carousel'      => Company\CarouselController::class,//品牌管理-首頁輪播
                'banner'        => Company\BannerController::class,//品牌管理-首頁輪播
                'news'          => Company\NewsController::class,//品牌管理-活動訊息
                'aboutus'       => Company\AboutUsController::class,//品牌管理-品牌設定-關於我們
                'product'       => Company\ProductIntroController::class,//品牌管理-品牌設定-產品介紹
                'qa'            => Company\QaController::class,//品牌管理-品牌設定-常見問題
                'others'        => Company\OthersettingController::class,//品牌管理-品牌設定-其他設定
                'role'          => Company\RoleController::class,//品牌管理-角色帳號管理-角色管理
                'user'          => Company\UserController::class,//品牌管理-角色帳號管理-帳號管理
            ]);

            // 行銷活動
            $router->prefix('campaign')->group(function(Router $router){
                $router->resources([
                    // 行銷活動
                    'overview'               => Company\Campaign\OverViewController::class,//品牌管理-活動設定-結帳優惠總覽
                    'reg/none/coupon'        => Company\Campaign\UserRegisterController::class,//品牌管理-活動設定-註冊活動-註冊禮
                    'reg/none/points'        => Company\Campaign\RegisterPointsController::class,//品牌管理-活動設定-註冊活動-註冊禮(送點數)
                    // 'order/cash/amount'   => Company\Campaign\CashAmountController::class,//品牌管理-活動設定-行銷活動-現金抵用券
                    // 'order/cash/discount' => Company\Campaign\CashDiscountController::class,//品牌管理-活動設定-行銷活動-現今折扣券
                    // 'order/cash/qty'      => Company\Campaign\CashQtyController::class,//品牌管理-活動設定-行銷活動-Y件免費券
                    'order/amount/amount'    => Company\Campaign\AmountController::class,//品牌管理-活動設定-行銷活動-滿多少錢合計金額折扣多少錢
                    // 'order/amount/qty'    => Company\Campaign\AmountForQtyController::class,//品牌管理-活動設定-行銷活動-滿額Y件免費
                    'order/amount/discount'  => Company\Campaign\DiscountForAmountController::class,//品牌管理-活動設定-行銷活動-滿多少錢合計金額打折
                    'order/qty/qty'          => Company\Campaign\QtyController::class,//品牌管理-活動設定-行銷活動-滿多少數量最低價幾件變多少錢
                    // 'order/qty/amount'    => Company\Campaign\QtyForAmountController::class,//品牌管理-活動設定-行銷活動-
                    // 'order/qty/discount'  => Company\Campaign\DiscountForQtyController::class,//品牌管理-活動設定-行銷活動-滿X件現金折扣
                    // 指定商品任選
                    'order/menu_group_n/qty/qty'       => Company\Campaign\MenuGroup\QtyForNQtyController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/那些商品最低價第幾件變多少錢
                    'order/menu_group_n/qty/amount'    => Company\Campaign\MenuGroup\QtyForNAmountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/那些商品第幾件金額折扣多少錢
                    'order/menu_group_n/qty/discount'  => Company\Campaign\MenuGroup\QtyForNDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/那些商品第幾件金額打折
                    'order/menu_group/qty/amount'      => Company\Campaign\MenuGroup\Qty4CheckoutAmountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/合計金額折扣多少錢
                    'order/menu_group/qty/discount'    => Company\Campaign\MenuGroup\Qty4CheckoutDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/合計金額打折
                    'order/menu_group/amount/amount'   => Company\Campaign\MenuGroup\Amount4CheckoutController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少錢/合計金額折扣多少錢
                    'order/menu_group/amount/discount' => Company\Campaign\MenuGroup\Amount4CheckoutDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少錢/合計金額打折
                    'order/menu_group/qty/menu_group/discount' => Company\Campaign\MenuGroup\QtyForDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/那些商品合計金額打折
                    'order/menu_group/qty/menu_group/qty' => Company\Campaign\MenuGroup\QtyForQtyController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/那些商品最低調幾件變多少錢
                    // 指定商品
                    'order/menu_item_n/qty/qty'       => Company\Campaign\MenuItem\QtyForNQtyController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/指定商品最低價第幾件變多少錢
                    'order/menu_item_n/qty/amount'    => Company\Campaign\MenuItem\QtyForNAmountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/指定商品第幾件金額折扣多少錢
                    'order/menu_item_n/qty/discount'  => Company\Campaign\MenuItem\QtyForNDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/指定商品第幾件金額打折
                    'order/menu_item/qty/menu_item/discount' => Company\Campaign\MenuItem\QtyForDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少數量/指定商品合計金額打折
                    'order/menu_item/amount/amount'   => Company\Campaign\MenuItem\Amount4CheckoutController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少錢/合計金額折扣多少錢
                    'order/menu_item/amount/discount' => Company\Campaign\MenuItem\Amount4CheckoutDiscountController::class,//品牌管理-活動設定-行銷活動-那些商品滿多少錢/合計金額打折
                    // 'complete/amount/amount'           => Company\Campaign\AmountController::class,//品牌管理-活動設定-行銷活動-滿額現金抵用
                    // 'complete/amount/qty'              => Company\Campaign\AmountForQtyController::class,//品牌管理-活動設定-行銷活動-滿額Y件免費
                    // 'complete/amount/discount'         => Company\Campaign\DiscountForAmountController::class,//品牌管理-活動設定-行銷活動-滿額現金折扣
                    // 'complete/qty/qty'                 => Company\Campaign\QtyController::class,//品牌管理-活動設定-行銷活動-滿X件Y件免費
                    // 'complete/qty/amount'              => Company\Campaign\QtyForAmountController::class,//品牌管理-活動設定-行銷活動-滿X件現金抵用
                    // 'complete/qty/discount'            => Company\Campaign\DiscountForQtyController::class,//品牌管理-活動設定-行銷活動-滿X件現金折扣
                ]);
                $router->get('index', 'Company\Campaign\SetupGuideController@index');//品牌管理-活動設定-結帳優惠設定導覽
            });

            // 優惠券
            $router->prefix('coupon')->group(function(Router $router){
                $router->resources([
                    'overview'              => Company\Coupon\OverViewController::class,//品牌管理-優惠券總覽
                    //'cash/unlimited'      => Company\CouponCashUnlimitedController::class,//品牌管理-優惠券現金折抵券-無限期
                    // 'cash/duration'      => Company\CouponCashDurationController::class,//品牌管理-優惠券現金折抵券--指定期限
                    // 'cash/after'         => Company\CouponCashAfterController::class,//品牌管理-優惠券現金折抵券--領後數日有效
                    // 'cash/week'          => Company\CouponCashWeekController::class,//品牌管理-優惠券現金折抵券--限定星期
                    // 'cash/amount'        => Company\Coupon\CouponCashAmountController::class,//品牌管理-優惠券-現金抵用券
                    // 'cash/discount'      => Company\Coupon\CouponCashDiscountController::class,//品牌管理-優惠券-現金折扣券
                    // 'cash/qty'           => Company\Coupon\CouponCashQtyController::class,//品牌管理-優惠券-Y件免費送
                    'order/amount/qty'      => Company\Coupon\CouponAmountQtyController::class,//品牌管理-優惠券--滿件Y件免費送
                    'order/amount/discount' => Company\Coupon\CouponAmountDiscountController::class,//品牌管理-優惠券--滿件打折
                    'order/amount/amount'   => Company\Coupon\CouponAmountAmountController::class,//品牌管理-優惠券--滿額折抵金額
                    'order/qty/amount'      => Company\Coupon\CouponQtyAmountController::class,//品牌管理-優惠券--滿件抵金額
                    'order/qty/discount'    => Company\Coupon\CouponQtyDiscountController::class,//品牌管理-優惠券--滿件打折
                    'order/qty/qty'         => Company\Coupon\CouponQtyQtyController::class,//品牌管理-優惠券--滿X件送Y件
                    // 那些商品
                    'order/menu_group_n/qty/qty'       => Company\Coupon\MenuGroup\QtyForNQtyController::class,//品牌管理-活動設定-優惠券-那些商品滿多少數量/那些商品最低價第幾件變多少錢
                    'order/menu_group_n/qty/amount'    => Company\Coupon\MenuGroup\QtyForNAmountController::class,//品牌管理-活動設定-優惠券-那些商品滿多少數量/那些商品第幾件金額折扣多少錢
                    'order/menu_group_n/qty/discount'  => Company\Coupon\MenuGroup\QtyForNDiscountController::class,//品牌管理-活動設定-優惠券-那些商品滿多少數量/那些商品第幾件金額打折
                    'order/menu_group/qty/amount'      => Company\Coupon\MenuGroup\Qty4CheckoutAmountController::class,//品牌管理-活動設定-優惠券-那些商品滿多少數量/合計金額折扣多少錢
                    'order/menu_group/qty/discount'    => Company\Coupon\MenuGroup\Qty4CheckoutDiscountController::class,//品牌管理-活動設定-優惠券-那些商品滿多少數量/合計金額打折
                    'order/menu_group/qty/menu_group/discount' => Company\Coupon\MenuGroup\QtyForDiscountController::class,//品牌管理-活動設定-優惠券-那些商品滿多少數量/那些商品合計金額打折
                    'order/menu_group/amount/amount'   => Company\Coupon\MenuGroup\Amount4CheckoutController::class,//品牌管理-活動設定-優惠券-那些商品滿多少錢/合計金額折扣多少錢
                    'order/menu_group/amount/discount' => Company\Coupon\MenuGroup\Amount4CheckoutDiscountController::class,//品牌管理-活動設定-優惠券-那些商品滿多少錢/合計金額打折
                ]);
                $router->get('index', 'Company\Coupon\SetupGuideController@index');//品牌管理-活動設定-優惠券設定導覽
            });

            // 點數相關-兌換商品設定
            $router->prefix('exchanges')->group(function(Router $router){
                $router->get('overview', 'Company\Exchanges\ExchangesController@overview'); // 總覽
                $router->get('guide', 'Company\Exchanges\ExchangesController@guide'); // 導覽頁
                $router->resource('overview', 'Company\Exchanges\ExchangesController')->only(['update']); // 總覽-(switch Use)
                $router->resource('item', Company\Exchanges\ItemController::class,     ['as' => 'company.exchanges']); // 實體商品操作
                $router->resource('coupon', Company\Exchanges\CouponController::class, ['as' => 'company.exchanges']); // 優惠券操作
            });

            $router->put('promotion/{id}/edit', 'Company\PromotionController@update');//品牌管理-促銷活動設定
            $router->post('promotion', 'Company\PromotionController@store');//品牌管理-促銷活動設定
            $router->resource('promotion', Company\PromotionController::class);//品牌管理-促銷活動設定

            $router->post('menu_item/ajax', 'Company\MenuItemController@updatePrice');
            $router->resource('menu_release', Company\MenuReleaseController::class);//總部菜單下放
            $router->post('menu_release/export', 'Company\MenuReleaseController@MenuExport');//匯出
            $router->post('menu_release/export_all', 'Company\MenuReleaseController@MenuExportAll');//匯出全部
        });

        $router->resources([
            'set_store_user/{store_id}/user'                => Company\StoreUserController::class,//品牌管理>>店家設定>>建立使用者(可以一直創)
            'set_group_item/{group_id}/menu_item'           => Company\GroupAddItemController::class,//品牌管理>>餐點管理>>餐點分類>>增加餐點
        ]);
        $router->resource('list/coupon', Company\Coupon\CouponController::class);//品牌管理-優惠券序號列表
    });
    $router->get('/logs/company/user', 'Company\UserController@log');//品牌管理-帳號LOG

//5.品牌報表
//6.店家管理
    $router->prefix('store')->group(function(Router $router){
        $router->get('/', 'HomeController@store_info');//店家-首頁資料
        $router->resource('list/orders', Store\Lists\OrdersController::class); //　店家-訂單列表
        $router->resource('list/billings', Store\Lists\BillingsController::class); //　店家-結帳單列表
        $router->prefix('set')->group(function(Router $router){
            $router->resource('store', Store\StoreController::class);//店家-商店基本資料
            $router->resources([
                'cuisine_group'   => Store\CuisineGroupController::class,//分店管理-餐點管理-餐點分類
                'menu_item'       => Store\MenuItemController::class,//分店管理-餐點管理-餐點分類-編輯餐點(透過URL進入做編輯餐點)
                'cuisine_unit'    => Store\CuisineUnitController::class,//分店管理-餐點管理-選項管理
                'menu_store_item' => Store\MenuStoreItemController::class,//分店管理-餐點管理-餐點分類-餐點清單
                'role'            => Store\RoleController::class,//分店管理-角色帳號管理-角色管理
                'user'            => Store\UserController::class,//分店管理-角色帳號管理-帳號管理
                'pos_account'     => Store\PosAccountController::class,//分店管理-角色帳號管理-POS機帳號管理
                'poshow'          => Store\PoshowController::class,//分店管理-POS子母畫面
            ]);

            // 行銷活動
            $router->prefix('campaign')->group(function(Router $router){
                $router->get('index', 'Store\Campaign\SetupGuideController@index');//店家管理-活動設定-結帳優惠設定導覽
                $router->resources([
                    'overview'              => Store\Campaign\OverViewController::class,//店家管理-活動設定-結帳優惠總覽
                    'order/cash/amount'     => Store\Campaign\CashAmountController::class, //店家管理-活動設定-行銷活動-現金抵用券
                    'order/cash/discount'   => Store\Campaign\CashDiscountController::class, //店家管理-活動設定-行銷活動-現今折扣券
                    'order/cash/qty'        => Store\Campaign\CashQtyController::class, //店家管理-活動設定-行銷活動-Y件免費券
                    'order/amount/amount'   => Store\Campaign\AmountController::class,//店家管理-活動設定-行銷活動-滿額現金抵用
                    'order/amount/qty'      => Store\Campaign\AmountForQtyController::class,//店家管理-活動設定-行銷活動-滿額Y件免費
                    'order/amount/discount' => Store\Campaign\DiscountForAmountController::class,//店家管理-活動設定-行銷活動-滿額現金折扣
                    'order/qty/qty'         => Store\Campaign\QtyController::class,//店家管理-活動設定-行銷活動-滿X件Y件免費
                    'order/qty/amount'      => Store\Campaign\QtyForAmountController::class,//店家管理-活動設定-行銷活動-滿X件現金抵用
                    'order/qty/discount'    => Store\Campaign\DiscountForQtyController::class,//店家管理-活動設定-行銷活動-滿X件現金折扣
                    'order/item/item'       => Store\Campaign\ItemController::class,//店家管理-活動設定-行銷活動-買指定商品非任選(數量)
                    'order/item/amount'     => Store\Campaign\ItemForAmountController::class,//店家管理-活動設定-行銷活動-買指定商品現金抵用
                    'order/item/random'     => Store\Campaign\ItemForRandomController::class,//店家管理-活動設定-行銷活動-買指定商品任選
                    'order/item/discount'   => Store\Campaign\DiscountForItemController::class,//店家管理-活動設定-行銷活動-買指定商品現金折扣
                ]);
            });
            // 優惠券
            $router->prefix('coupon')->group(function(Router $router){
                $router->get('index', 'Store\Coupon\SetupGuideController@index');//店家管理-活動設定-優惠券設定導覽
                $router->resources([
                    'overview'        => Store\Coupon\OverViewController::class,//店家管理-優惠券總覽
                    'amount/qty'      => Store\Coupon\CouponAmountQtyController::class,//店家管理-優惠券--滿件Y件免費送
                    'amount/discount' => Store\Coupon\CouponAmountDiscountController::class,//店家管理-優惠券--滿件打折
                    'amount/amount'   => Store\Coupon\CouponAmountAmountController::class,//店家管理-優惠券--滿額折抵金額
                    'qty/amount'      => Store\Coupon\CouponQtyAmountController::class,//店家管理-優惠券--滿件抵金額
                    'qty/discount'    => Store\Coupon\CouponQtyDiscountController::class,//店家管理-優惠券--滿件打折
                    'qty/qty'         => Store\Coupon\CouponQtyQtyController::class,//店家管理-優惠券--滿X件送Y件
                    'item/item'       => Store\Coupon\CouponItemItemController::class,//店家管理-優惠券--指定商品非任選(數量)
                    'item/amount'     => Store\Coupon\CouponItemAmountController::class,//店家管理-優惠券--指定商品抵金額
                    'item/random'     => Store\Coupon\CouponItemRandomController::class,//店家管理-優惠券--指定商品任選
                    'item/discount'   => Store\Coupon\CouponItemDiscountController::class,//店家管理-優惠券--指定商品打折
                ]);
            });
            // 點數相關-兌換商品設定
            $router->prefix('exchanges')->group(function(Router $router){
                $router->get('overview', 'Store\Exchanges\ExchangesController@overview'); // 店家管理-點數相關-兌換商品設定-總覽
                $router->get('guide', 'Store\Exchanges\ExchangesController@guide'); // 店家管理-點數相關-兌換商品設定-導覽頁
                $router->resource('overview', 'Store\Exchanges\ExchangesController')->only(['update']); // 店家管理-點數相關-兌換商品設定-總覽-(switch Use)
                $router->resource('item', Store\Exchanges\ItemController::class,     ['as' => 'store.exchanges']); // 店家管理-點數相關-兌換商品設定-導覽頁-實體商品操作
                $router->resource('coupon', Store\Exchanges\CouponController::class, ['as' => 'store.exchanges']); // 店家管理-點數相關-兌換商品設定-導覽頁-優惠券操作
            });

            $router->resource('/menu_import', Store\MenuImportController::class);
            $router->post('/menu_import/import', 'Store\MenuImportController@MenuImport');//匯入
            $router->post('/menu_import/import_all', 'Store\MenuImportController@MenuImportAll');//匯入
            $router->post('/publish/menu', 'Store\MenuImportController@MenuPublish');//發佈菜單
            $router->post('/menu_item/ajax', 'Store\MenuItemController@updatePrice');//店家-
            $router->post('/publish/poshow', 'Store\PoshowController@publish');//發佈子母畫面
            $router->post('/publish/campaign', 'Store\Campaign\OverViewController@publish');//發佈行銷活動
            $router->resource('/product_exchange', Store\ProductExchangeController::class);//店家商品兌換
        });
        $router->resources([
            'set_group_item/{group_id}/menu_item'     => Store\GroupAddItemController::class,//分店管理-餐點管理-餐點分類-增加餐點
        ]);

        $router->resource('profile', Store\ProfileController::class);//店家-商店店面設定
        //$router->resource('orders', OrdersController::class);//店家-
        $router->get('overview', 'Store\StoreController@overview');
        $router->get('menu', 'Store\StoreController@overview');//店家-

        // $router->get('/test', 'Store\ProfileController@test');
        $router->get('/orders_detail/{id?}', 'Store\OrdersDetailController@index');//店家訂單明細
        $router->post('/rollback/product_exchange', 'Store\OrdersController@rollBack');//店家修訂訂單之商品兌換狀態
        //$router->resource('/point', Store\PointController::class);//店家手動扣點
        $router->get('/point', 'Store\PointController@index');//店家手動扣點
        //$router->get('/point/account/search', 'Store\PointController@accountSearch');//店家手動扣點
        $router->get('/point/account/search', 'Store\PointController@search');//店家手動扣點
        $router->post('/point/management', 'Store\PointController@pointProcessing');//店家手動扣點
    });
    $router->get('/logs/store/user', 'Store\UserController@log');//分店管理-帳號LOG
//7.店家報表
    $router->prefix('store/reports')->group(function(Router $router){
        //店家報表>>訂單銷售
        $router->get('statistics/query', 'Store\Reports\StatisticsController@index');//店家報表>>訂單銷售
        $router->any('statistics/load_year_orders', 'Store\Reports\StatisticsController@loadYearOrders');//店家報表>>訂單銷售>>API
        $router->any('statistics/load_order_items', 'Store\Reports\StatisticsController@loadOrderItems');//店家報表>>訂單銷售>>API
        //店家報表>>日期銷售
        $router->get('daily_sale/query', 'Store\Reports\DailySalesController@index');//店家報表>>日期銷售
        $router->get('daily_sale/search', 'Store\Reports\DailySalesController@search');//店家報表>>日期銷售查詢
        $router->get('daily_sale/chart', 'Store\Reports\DailySalesController@load_chart_data');//店家報表>>日期銷售>>API
        //店家報表>>時段銷售
        $router->get('period_sale/query', 'Store\Reports\PeriodSalesController@index');//店家報表>>時段銷售
        $router->get('period_sale/search', 'Store\Reports\PeriodSalesController@search');//店家報表>>時段銷售查詢
        $router->get('period_sale/chart', 'Store\Reports\PeriodSalesController@load_chart_data');//店家報表>>時段銷售>>API
        //店家報表>>餐點品項銷售
        $router->get('item_sale/query', 'Store\Reports\ItemSalesController@index');//店家報表>>餐點品項銷售
        $router->get('item_sale/search', 'Store\Reports\ItemSalesController@search');//店家報表>>餐點品項銷售查詢
        $router->get('item_sale/chart', 'Store\Reports\ItemSalesController@load_chart_data');//店家報表>>餐點品項銷售>>API
        //店家報表>>餐點分類銷售
        $router->get('group_sale/query', 'Store\Reports\ItemGroupSalesController@index');//店家報表>>餐點分類銷售
        $router->get('group_sale/search', 'Store\Reports\ItemGroupSalesController@search');//店家報表>>餐點分類銷售查詢
        $router->get('group_sale/chart', 'Store\Reports\ItemGroupSalesController@load_chart_data');//店家報表>>餐點分類銷售>>API
        //店家報表>>折扣類型
        $router->get('campaign/query', 'Store\Reports\CampaignTypeController@index');//店家報表>>折扣類型
        $router->get('campaign/search', 'Store\Reports\CampaignTypeController@search');//店家報表>>折扣類型查詢
        $router->get('campaign/chart', 'Store\Reports\CampaignTypeController@load_chart_data');//店家報表>>折扣類型>>API
        //店家報表>>訂單類型
        $router->get('order_type/query', 'Store\Reports\OrderTypeController@index');//店家報表>>訂單類型
        $router->get('order_type/search', 'Store\Reports\OrderTypeController@search');//店家報表>>訂單類型查詢
        $router->get('order_type/count_chart', 'Store\Reports\OrderTypeController@load_count_data');//店家報表>>訂單類型>>API
        $router->get('order_type/amount_chart', 'Store\Reports\OrderTypeController@load_amount_data');//店家報表>>訂單類型>>API
        //店家報表>>付款方式
        $router->get('payment/query', 'Store\Reports\PaymentController@index');//店家報表>>付款方式
        $router->get('payment/search', 'Store\Reports\PaymentController@search');//店家報表>>付款方式查詢
        $router->get('payment/src_amount_chart', 'Store\Reports\PaymentController@load_src_amount_data');//店家報表>>付款方式>>API
        $router->get('payment/amount_chart', 'Store\Reports\PaymentController@load_amount_data');//店家報表>>付款方式>>API
    });
//8.系統管理
//9.Helpers
//10.暫存&測試區
    $router->resource('/test/demo', TestDemoController::class);
});
