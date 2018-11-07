<?php

namespace App\Model\system_member;

use Illuminate\Database\Eloquent\Model;

class MemberDetail extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'member_detail';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'member_id', 'company_id', 'uuid', 'facebook',
        'line', 'name', 'email', 'phone', 'captcha',
        'valid', 'login_error_num', 'lock', 'status', 'login_token',
        'login_at'
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
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

}
