<?php
namespace App\Console\Coupons;

// 核心
use Illuminate\Console\Command;

use App\Models\idelivery\Campaign_setting;
use App\Models\idelivery\Coupon;
use Carbon\Carbon;

/**
 * 愛外送 產生序號
 * php artisan CouponSnGenJob:CouponSnGenJob
 */
class CouponSnGenJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CouponSnGenJob:CouponSnGenJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '優惠券序號產出成功';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info('START');
        // 序號設定檔.(type)2:優惠券.(sn_gen)1:先產.(sn_gen_status)0:未產完.(end_at)還沒過期
        $CouponSettings = Campaign_setting::where('types', 2)
            ->where('sn_gen', 1)
            ->where('sn_gen_status', 0)
            ->where('end_at', '>', Carbon::now())
            ->get();
        if ($CouponSettings->isNotEmpty()) {
            $this->info('有優惠券設定');
            $now = date("Y-m-d H:i:s");
            $qty_start = 0;
            $qty_max = 0;
            foreach ($CouponSettings as $Setting) {
                // 上鎖
                $Setting->locks = 1;
                $Setting->save();
                if(empty($Setting->max_qty)){
                    // 無限量
                    $this->info('無限量優惠卷');
                    // 剩餘可使用數量.(setting_id)優惠卷設定ID.(member_detail_id)未發送.(locks)0:未鎖.(status)1:啟用
                    $coupon_count = Coupon::where("setting_id", $Setting->id)
                        ->where('member_detail_id', 0)
                        ->where('locks', 0)
                        ->where('status', 1)
                        ->count();
                    // 預產序號是否充足
                    if($coupon_count <= 500){
                        $qty_start = $coupon_count;
                        $qty_max = 500;
                    }
                }else{
                    // 限量
                    $this->info('限量優惠卷');
                    // 已產生序號總筆數.(setting_id)優惠卷設定ID
                    $coupon_count = Coupon::where("setting_id", $Setting->id)->count();
                    // 目標序號是否滿足
                    if($coupon_count < $Setting->max_qty){
                        $qty_start = $coupon_count;
                        $qty_max = $Setting->max_qty;
                    }else{
                        // 發送完成
                        $Setting->sn_gen_status = 1;
                        $Setting->save();
                    }
                }
                $this->info('開始筆數:' . $qty_start);
                $this->info('結束筆數:' . $qty_max);
                for($i = $qty_start;$i < $qty_max;$i++){
                    // 序號
                    $sn = md5(uniqid("", true));
                    // 寫入優惠卷
                    $db = Coupon::create(array(
                        'company_id' => $Setting->company_id,
                        'store_id' => $Setting->store_id,
                        'setting_id' => $Setting->id,
                        'sn' => $sn,
                        'week_days' => $Setting->week_days,
                        'start_at' => $Setting->start_at,
                        'end_at' => $Setting->end_at,
                        'counts' => $Setting->user_use_count,
                        'created_at' => $now
                    ));
                }
            }
            $this->info('排程執行完成!');
        }
        $this->info('END');
    }
}