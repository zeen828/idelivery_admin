<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;

class TestDemo extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'test_demo';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'username', 'name', 'avatar', 'remember_token'
    ];
    // 隱藏不顯示欄位
    protected $hidden = ['password'];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    //const CREATED_AT = 'create_time';
    //const UPDATED_AT = 'updated_at';
}
