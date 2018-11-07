<?php

namespace App\Model\system_member;

use Illuminate\Database\Eloquent\Model;

class PointConsumeLog extends Model
{    
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'point_consume_log';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'member_id', 'company_id', 'store_id', 'member_detail_id', 
        'operating_role', 'description', 'point_type_id', 'exchange_type', 'exchange_src_id', 
        'point_deducted_total', 'point_id', 'point_original', 'point_deducted', 'point_later', 
        'created_at', 'updated_at'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    //public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    //const CREATED_AT = 'created_at';
    //const UPDATED_AT = 'updated_at';
 

    /**
     * 取得點數資料
     * 
     *  @param exchange_src_id      = [必填] 兌換紀錄編號
     *
     * return TRUE = 成功 OR FALSE = 失敗
     */
    public static function getExchangeView($exchange_src_id)
	{
        $result = self::join('point', 'point.id', '=', 'point_consume_log.point_id')
            ->where('exchange_src_id', $exchange_src_id)
            ->where('exchange_type', 'exchanges')
            ->select()
            ->get('point_consume_log.*', 'point.expired_at', 'point.order_id');

        return $result;
    }

}
