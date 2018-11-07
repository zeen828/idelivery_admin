<?php
namespace App\Console\Coupons;

use Illuminate\Console\Command;
use App\Models\system_member\Member;
use App\Models\system_member\Member_detail;
use App\Models\idelivery\Coupon_schedule_log;
use App\Models\idelivery\Campaign_setting;
use App\Models\idelivery\Coupon;

/**
 * 愛外送 發放優惠卷
 * php artisan CouponSnSendJob:CouponSnSendJob
 */
class CouponSnSendJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'CouponSnSendJob:CouponSnSendJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '優惠券發放成功';

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

        // 待發送紀錄.(exec_status)0:未執行
        $list = Coupon_schedule_log::where('exec_status', 0)->get();
        if ($list->isNotEmpty()) {
            foreach ($list as $value) {
                // 沒有設定值設定錯誤
                if (empty($value->setting_id) || empty($value->member_id)) {
                    $value->exec_status = 1;
                    $value->message = '會員編號或優惠券設定編號遺失';
                    $value->save();
                    continue;
                }
                // 檢查暫定會員編號
                $member = Member::find($value->member_id);
                if (empty($member)) {
                    $value->exec_status = 1;
                    $value->message = '查無暫定會員編號';
                    $value->save();
                    continue;
                }

                // 檢查優惠券設定
                $setting = Campaign_setting::find($value->setting_id);
                // 上鎖
                $setting->locks = 1;
                $setting->save();
                // 檢查優惠卷設定
                if (empty($setting)) {

                    $value->exec_status = 1;
                    $value->message = '查無優惠券設定編號';
                    $value->save();

                    continue;
                }

                $now = date("Y-m-d H:i:s");


                // 序號先產後產
                if ($setting->sn_gen == 2) {
                    if (empty($setting->kind_value)) {
                        $value->message = '查無領後幾天設定檔內容';
                        $value->save();

                        continue;
                    }
                    // 後產
                    $sn = md5(uniqid("", true));
                    $start_at = $now;
                    $end_at = date("Y-m-d H:i:s", strtotime($now .'+'.$setting->kind_value.' days'));

                    // 取得序號筆數
                    $send_count = Coupon::where([
                        'member_id'        => $value->member_id,
                        'member_detail_id' => $value->member_detail_id,
                        'company_id'       => $setting->company_id,
                        'store_id'         => $setting->store_id,
                        'setting_id'       => $value->setting_id,
                        'status'           => 1,
                    ])->count();

                    if ($setting->max_qty > $send_count || $setting->max_qty == 0) {
                        $db = Coupon::create([
                            'member_id'        => $value->member_id,
                            'member_detail_id' => $value->member_detail_id,
                            'company_id'       => $setting->company_id,
                            'store_id'         => $setting->store_id,
                            'setting_id'       => $value->setting_id,
                            'sn'               => $sn,
                            'start_at'         => $start_at,
                            'end_at'           => $end_at,
                            'counts'           => $setting->user_use_count,
                        ]);

                        if ($db === false) {

                            //$value->exec_status = 1;
                            $value->message = '優惠券新增錯誤';
                            $value->save();

                            continue;
                        }

                        $value->exec_status = 1;
                        $value->coupon_id = $db->id;
                        $value->status = 1;
                        $value->message = '優惠券發放成功';
                        $result = $value->save();

                        if ($result === false) {

                            //$value->exec_status = 1;
                            $value->message = '優惠券發放記錄寫入錯誤';
                            $value->save();

                            continue;
                        }
                    }
                } else {
                    // 先產
                    $curr_coupon = Coupon::where([
                        ['company_id', '=', $setting->company_id],
                        ['store_id', '=', $setting->store_id],
                        ['setting_id', '=', $value->setting_id],
                        ['member_detail_id', '=', 0],
                        ['end_at', '>', $now],
                        ['locks', '=', 0],
                        ['status', '=', 1]
                    ])
                    ->orderBy('id', 'asc')
                    ->first();

                    if (!empty($curr_coupon)) {
                        $curr_coupon->member_detail_id = $value->member_detail_id;
                        $result = $curr_coupon->save();

                        if ($result === false) {
                            $value->exec_status = 1;
                            $value->message = '優惠券發放錯誤';
                            $value->save();

                            continue;

                        } else {
                            $value->coupon_id = $curr_coupon->id;
                            $value->exec_status = 1;
                            $value->status = 1;
                            $value->message = '優惠券發放成功';
                            $value->save();
                        }
                    } else {
                        $value->exec_status = 1;
                        $value->message = '查無可用優惠券';
                        $value->save();

                        continue;
                    }
                }

                $this->info('排程執行完成!');
            }
        }

        $this->info('END');
    }
}