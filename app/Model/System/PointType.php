<?php

namespace App\Model\System;

use Illuminate\Database\Eloquent\Model;
use DB;

class PointType extends Model
{    
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'point_type';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'name', 'slug', 'status'
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

   
    public static function getList()
    {
        $result = DB::table('point_type')
            ->where('status', '=', '1')
            ->orderBy('id', 'asc')->get();

       return $result;
    }

}
