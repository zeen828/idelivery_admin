<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;

class StoreDeliveryCondition extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'store_delivery_condition';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = ['store_id', 'scope', 'type', 'value', 'create_at', 'update_at'];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    // use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    // protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'update_at';
}