<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;

class CompanyStoreBusinessHours extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'company_store_business_hours';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = ['store_id', 'type', 'week_day', 'start_time', 'end_time', 'create_at', 'updated_at'];
    // 隱藏不顯示欄位
    // protected $hidden = ['password'];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = false;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'create_at';
    const UPDATED_AT = 'updated_at';

    // protected $casts = [
    //     'week_day' => 'array'
    // ];

    public function setWeekDayAttribute($value)
    {
        $this->attributes['week_day'] = ! is_null ($value) ? implode(',', $value) : '';
    }
}
