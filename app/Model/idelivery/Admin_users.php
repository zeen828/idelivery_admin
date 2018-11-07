<?php

namespace App\Model\idelivery;

use App\Model\idelivery\Store;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Admin_users extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'admin_users';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'admin_user_id', 'username', 'name', 'avatar', 'remember_token',
        'captcha'
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

    /**
     * 取得店家會員
     */
    public function getManagementUser()
    {
        //var_dump($stoer_id);
        return $this->select('admin_users.*')->rightJoin('admin_user_store', 'admin_user_store.user_id', '=', 'admin_users.id')
            ->whereIn('admin_users.admin_user_id', [1, 2])
            ->distinct('admin_users.username');
    }

    /**
     * 取得總店會員
     */
    public function getCompanyUser($stoer_id=0)
    {
        //var_dump($stoer_id);
        //取得店家所屬總店
        $stoer = Store::find($stoer_id);
        //var_dump($stoer);
        $head_store_id = $stoer->head_store_id;
        //var_dump($head_store_id);
        return $this->select('admin_users.*')->rightJoin('admin_user_store', 'admin_user_store.user_id', '=', 'admin_users.id')
            ->whereNotIn('admin_users.admin_user_id', [0, 2])
            ->where('admin_user_store.store_id', '=', $head_store_id)
            ->distinct('admin_users.username');
    }

    /**
     * 取得店家會員
     */
    public function getStoeeUser($stoer_id=0)
    {
        //var_dump($stoer_id);
        return $this->select('admin_users.*')->rightJoin('admin_user_store', 'admin_user_store.user_id', '=', 'admin_users.id')
            ->whereNotIn('admin_users.admin_user_id', [0, 2])
            ->where('admin_user_store.store_id', '=', $stoer_id)
            ->distinct('admin_users.username');
    }

    /**
     * 取得父
     */
    public function father()
    {
        return $this->hasMany('App\Model\idelivery\Admin_users', 'id', 'admin_user_id');
    }

    /**
     * 取得子
     */
    public function child()
    {
        return $this->hasMany('App\Model\idelivery\Admin_users', 'admin_user_id', 'id');
    }

    /**
     * 多對多關聯角色
     */
    public function roles()
    {
        return $this->belongsToMany('App\Model\idelivery\Admin_roles', 'admin_role_users', 'user_id', 'role_id');
    }

    /**
     * 多對多關聯店家
     */
    public function store()
    {
        return $this->belongsToMany('App\Model\idelivery\Store', 'admin_user_store', 'user_id', 'store_id', 'id', 'id');
    }

    public static function getStoeeAdminUser($stoer_id=0){
        return self::select('admin_users.username')
            ->leftJoin('admin_user_store', 'admin_user_store.user_id', '=', 'admin_users.id')
            ->where('admin_user_store.store_id', '=', $stoer_id)
            ->whereIn('admin_users.admin_user_id', [1, 2])// 透過admin或是webadmin建立的
            ->first();// 只取一筆
            //->get();// 只取多筆
            //->toSql();// 輸出SQL語法
    }

    public static function getStoeeAdminUsers($stoer_id=0){
        return self::select('admin_users.username')
            ->leftJoin('admin_user_store', 'admin_user_store.user_id', '=', 'admin_users.id')
            ->where('admin_user_store.store_id', '=', $stoer_id)
            ->where('admin_users.id', '!=', '1')// 透過admin或是webadmin建立的
            //->first();// 只取一筆
            ->get();// 只取多筆
            //->toSql();// 輸出SQL語法
    }
}
