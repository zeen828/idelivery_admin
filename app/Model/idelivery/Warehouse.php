<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Warehouse extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'warehouse';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'company_id', 'store_id', 'warehouse_name', 'post_code', 
        'address', 'created_at', 'updated_at' 
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    //protected $dates = ['deleted_at'];
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public static function getFirstID($service_id, $company_id, $store_id = 0)
    {
        $warehouse = self::where('service_id', $service_id)
                    ->where('company_id', $company_id)
                    ->where('store_id', $store_id)
                    ->select('id')
                    ->orderBy('id')
                    ->first();

        return $warehouse->id;
    }
}
