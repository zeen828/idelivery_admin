<?php

namespace App\Model\System;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Point extends Model
{    
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'point';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'member_id', 'company_id', 'store_id', 'member_detail_id', 
        'operating_role', 'description', 'point_type_id', 'order_id', 'point',
        'point_surplus', 'expired_at', 'created_at', 'updated_at'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
   
    public function getList($service_id, $company_id)
    {
        $result = self::join('member_detail', 'point.member_id', '=', 'member_detail.member_id')
            ->join('member', 'point.member_id', '=', 'member.id')
            ->join('point_type', 'point.point_type_id', '=', 'point_type.id')    
            ->where('member_detail.service_id', '=', $service_id)
            ->where('member_detail.company_id', '=', $company_id)
            ->select('member_detail.name', 'member.account', 'point_type.name as point_type',  
                DB::raw('SUM(point.point_surplus) as point_surplus'), 'point.expired_at')
            ->groupBy('member_detail.name', 'member.account', 'point_type.name', 'point.expired_at')
            ->orderBy('member.account', 'asc');
                    

       return $result;
    }


    /**
     * Save interface.
     *
     * @param $data
     * @return Content
     */
    public static function add($data)
    {
        $result = self::insert($data);

        return $result;
    }


    /**
     * 扣除點數資料
     * 
     * @param (array) data     = [必填] 刪除條件陣列
     * {
     *  @param service_id           = [必填] 服務代碼
     *  @param member_id            = [必填] 會員ID
     *  @param company_id           = [必填] 公司編號
     *  @param store_id             = [必填] 店家編號
     *  @param member_detail_id     = [必填] 正式會員編號
     *  @param operating_role       = [必填] 操作角色
     *  @param description          = [必填] 說明
     *  @param point_type_id        = [必填] 點數類型
     *  @param point_deducted_total = [必填] 扣除總點數
     *  @param exchange_type        = [必填] 兌換種類編號 (0:人工, 1:訂單, 2:兌換)
     *  @param exchange_id          = [必填] 兌換ID/訂單ID(空的就是後台灌點)
     * }
     * 
     * return TRUE = 成功 OR FALSE = 失敗
     */
    public static function reduce($data)
	{        
        $result = DB::transaction(function () use ($data) {

            //$now = date("Y-m-d H:i:s");

            $total = DB::connection('system_member')
                ->select('SELECT SUM(point_surplus) as point_surplus FROM point WHERE service_id = ? AND company_id = ? 
                        AND (member_id = ? OR member_detail_id = ?) AND point_type_id = ? 
                        AND point_surplus > 0 AND expired_at > ?', 
                        [$data['service_id'], $data['company_id'], $data['member_id'], $data['member_detail_id'], 
                        $data['point_type_id'], Carbon::now()]);

            if (!empty($total))
            {
                //點數餘額不足
                if ($data['point_deducted_total'] - $total[0]->point_surplus > 0)
                {
                    return false;
                }
            }

            //總扣除點數/剩餘未扣除點數
            $point_later = $data['point_deducted_total'];
            $exchange_type = empty($data['exchange_type']) ? 'order' : $data['exchange_type'];
            $exchange_id = empty($data['exchange_src_id']) ? 0 : $data['exchange_src_id'];

            While ($point_later > 0)
            {
                $points = DB::connection('system_member')
                    ->select('SELECT id, point_surplus FROM point WHERE service_id = ? AND company_id = ? 
                            AND (member_id = ? OR member_detail_id = ?) AND point_type_id = ? 
                            AND point_surplus > 0 AND expired_at > ? ORDER BY id LIMIT ?', 
                            [$data['service_id'], $data['company_id'], $data['member_id'], $data['member_detail_id'], 
                            $data['point_type_id'], Carbon::now(), $point_later]);

                if (empty($points))
                {
                    return false;
                }

                //本筆加點紀錄之剩餘點數
                $point_reamin = 0;
                //本筆加點紀錄之扣除點數
                $point_deducted = 0;

                foreach ($points as $point)
                {
                    $now = date("Y-m-d H:i:s");

                    if ($point_later - $point->point_surplus >= 0)
                    {
                        $point_later = $point_later - $point->point_surplus;
                        $point_deducted = $point->point_surplus;
                        $point_reamin = 0;
                    }
                    else
                    {
                        $point_deducted = $point_later;
                        $point_reamin = $point->point_surplus - $point_later;
                        $point_later = 0;
                    }

                    DB::connection('system_member')
                        ->insert('INSERT INTO point_consume_log (service_id, member_id, company_id, store_id, 
                            member_detail_id, operating_role, description, point_type_id, exchange_type, 
                            exchange_src_id, point_deducted_total, point_id, point_original, point_deducted,
                            point_later, created_at) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', 
                            [$data['service_id'], $data['member_id'], $data['company_id'],
                            $data['store_id'], $data['member_detail_id'], $data['operating_role'],
                            $data['description'], $data['point_type_id'], $exchange_type, $exchange_id, 
                            $data['point_deducted_total'], $point->id, $point->point_surplus, $point_deducted,
                            $point_later, Carbon::now()]);

                        DB::connection('system_member')
                            ->update('UPDATE point SET point_surplus = ?, updated_at = ? WHERE id = ?', 
                                [$point_reamin, Carbon::now(), $point->id]);

                    if ($point_later <= 0)
                    {
                        break;
                    }
                }
            }

        }, 5);

        return $result;
    }

}
