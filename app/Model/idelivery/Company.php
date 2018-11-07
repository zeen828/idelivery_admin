<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
//use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'company';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'brand', 'name', 'uniform_numbers', 'supervisor_name', 'supervisor_phone',
        'district_id', 'city_id', 'post_code', 'district_name', 'address',
        'about', 'image', 'profit', 'bank', 'bank_branch',
        'bank_account', 'passbook_picture', 'remarks', 'status'
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
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'updated_at';


    /**
     * 取得上線店家數
     * 
     * @var company_id 公司編號
     */
    public static function getStoreCount($company_id)
    {
        return DB::table('store')
                    ->where(array(['status', '1'],['company_id', $company_id]))
                    ->count();
    }


    /**
     * 取得上線vip店家數
     * 
     * @var company_id 公司編號
     */
    public static function getVipStoreCount($company_id)
    {
        return DB::table('store')
                    ->where(array(['status', '1'],['company_id', $company_id],['is_cooperation', '1']))
                    ->count();
    }
}
