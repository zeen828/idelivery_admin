<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;

class Admin_account extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'admin_account';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'company_id', 'company_name', 'store_id', 'store_name', 'admin_account_store_id',
        'uuid', 'account', 'name', 'phone', 'email',
        'captcha', 'status', 'last_login_time', 'create_time', 'first_login',
        'login_token'
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
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'create_time';

    /**
     * 一對一
     * 品牌
     */
    public function company()
    {
        return $this->hasOne('App\Model\idelivery\Company', 'id', 'company_id');
    }

    /**
     * 一對一
     * 店家
     */
    public function store()
    {
        return $this->hasOne('App\Model\idelivery\Store', 'id', 'store_id');
    }

    /**
     * 一對一
     * 店家端帳號角色
     */
    public function admin_account_store()
    {
        return $this->hasOne('App\Model\idelivery\admin_account_store', 'id', 'admin_account_store_id');
    }
}
