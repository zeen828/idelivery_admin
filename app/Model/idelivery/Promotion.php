<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'promotion';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'company_id', 'store_id', 'start_date', 'end_date', 
        'point_type_id', 'point', 'amount', 'qty', 'weekly', 
        'daily', 'item', 'options', 'expired', 'status', 
        'created_at', 'updated_at'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    // public function getWeeklyAttribute($value)
    // {
    //     $weekly = json_decode($value, true);
    //     $week = 0;
    //     if (!empty($weekly))
    //     {
    //         $week = $weekly[0]['week'];
    //     }

    //     return $week;
    // }

    public function getList($service_id, $company_id, $store_id = 0)
    {
        $result = self::where('service_id', $service_id)
            ->where('company_id', $company_id)
            ->where('store_id', $store_id);

        return $result;
        
    }

    public static function getData($service_id, $company_id, $store_id = 0)
    {
        $result = self::where('service_id', $service_id)
            ->where('company_id', $company_id)
            ->where('store_id', $store_id)
            ->select('*')
            ->first();

        return $result;
        
    }

    public static function getView($id)
    {
        $result = self::where('id', $id)
            ->select('*')->first();

        return $result;
        
    }

    public static function add($data)
    {
        return self::insert([$data]);
    }

    public static function updateData($id, $data)
    {
        return self::where('id', $id)
                ->update($data);
    }
}
