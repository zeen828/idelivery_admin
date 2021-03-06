<?php

namespace App\Model\system_member;

use Illuminate\Database\Eloquent\Model;

class SmsLog extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'sms_log';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'company_id', 'store_id', 'status', 'kmsgid', 
        'dstaddr', 'smbody', 'dlvtime', 'donetime', 'statusstr', 
        'curl_str', 'created_at', 'updated_at'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = ['password'];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    //取得簡訊紀錄內容
    public static function getList()
    {
        return self::orderBy('created_at', 'desc');
    }

    //取得指定簡訊編號紀錄內容
    public static function getView($id)
    {
        return self::where('id', $id)->first();
    }
    

    //加入簡訊排程
    public static function add($data)
    {
        return self::insert($data);
    }
}
