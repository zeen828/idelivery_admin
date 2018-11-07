<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // 註冊排程
        '\App\Console\Coupons\CouponSnGenJob',//優惠券產生序號
        '\App\Console\Coupons\CouponSnSendJob',//優惠券發送序號
        '\App\Console\Invoice\EZinvoiceGenJob',//電子發票發送(串接新電)
        //'\App\Console\Invoice\EZinvoiceCancelJob',//電子發票作廢(串接新電)
        '\App\Console\Invoice\InvoiceQueryJob',//電子發票作廢(串接新電)
        '\App\Console\Invoice\EZinvoiceUploadCheckJob',//電子發票上傳數量檢查(串接新電)
        '\App\Console\Reports\ReportOrderGenJob',//報表統計-訂單
        '\App\Console\Reports\ReportOrderDetailGenJob',//報表統計-訂單明細
        '\App\Console\Reports\ReportCampaignGenJob',//報表統計-活動/優惠
        '\App\Console\Points\PointsSendJob',//點數發放
        '\App\Console\Reports\ReportBillingGenJob',//報表統計-結帳單
        '\App\Console\Reports\ReportBillingDetailGenJob',//報表統計-結帳單明細
        '\App\Console\Reports\ReportBillingCampaignGenJob',//報表統計-結帳活動/優惠
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
//         //執行排程
//         $schedule->command('CouponSnSendJob:CouponSnSendJob')
//                  ->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
