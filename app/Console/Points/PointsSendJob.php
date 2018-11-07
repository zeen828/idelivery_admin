<?php
/**
 * Created by PhpStorm.
 * User: cecyu
 * Date: 2018/8/1
 * Time: 上午 10:22
 */

namespace App\Console\Points;

// 核心
use Illuminate\Console\Command;

use App\Models\idelivery\Points_schedule_log;
use App\Models\system_member\Point;


/**
 * 愛外送 點數發放
 * php artisan Points:PointsSendJob
 */
class PointsSendJob extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Points:PointsSendJob';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '點數發放排程';

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

        //取出點數未發放紀錄資料
        $points_schedule_log = Points_schedule_log::where("status", 0)->get();
        if (!empty($points_schedule_log))
        {
            foreach ($points_schedule_log as $log)
            {
                $point = $this->points_format();

                $point["service_id"]        = $log->service_id;
                $point["member_id"]         = $log->member_id;
                $point["company_id"]        = $log->company_id;
                $point["store_id"]          = $log->store_id;
                $point["member_detail_id"]  = $log->member_detail_id;
                $point["description"]       = $log->description;
                $point["point_type_id"]     = $log->point_type_id;
                $point["point"]             = $log->points;
                $point["point_surplus"]     = $log->points;
                $point["expired_at"]        = $log->expired_at;

                $result = Point::create($point);
                if ($result === false)
                {
                    $log->status = 2;   //點數發放執行失敗
                }
                else
                {
                    $log->status = 1;   //點數發放執行成功
                }
                $log->save();
            }
        }

        $this->info('END');
    }


    private function points_format()
    {
        $format_array = array(
            "service_id"        => 0,
            "member_id"         => 0,
            "company_id"        => 0,
            "store_id"          => 0,
            "member_detail_id"  => 0,
            "operating_role"    => "System",
            "description"       => null,
            "point_type_id"     => 0,
            "order_id"          => 0,
            "point"             => 0,
            "point_surplus"     => 0,
            "expired_at"        => null,
        );
        return $format_array;

    }
}