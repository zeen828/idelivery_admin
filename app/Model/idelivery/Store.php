<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;//軟刪除
use Illuminate\Support\Facades\DB;

class Store extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'store';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'company_id', 'head_store_id', 'area_id', 'business_registration', 'uniform_numbers', 'invoice_aes_key',
        'name', 'district_id', 'city_id', 'post_code', 'district_name',
        'address', 'latitude', 'longitude', 'order_phone', 'order_fax',
        'order_mobile_phone', 'image', 'status', 'description', 'intro_url',
        'supervisor_name', 'supervisor_phone', 'supervisor_email', 'is_cooperation', 'off_date',
        'business_hours', 'order_hours', 'carry_out_conditions', 'allow_order_delivery', 'take_out',
        'delivery_order', 'delivery_conditions', 'delivery_interval_quota', 'order_flow', 'promotion_amount',
        'promotion_discount', 'sw_reward', 'sw_r_point', 'sw_r_campaign', 'sw_use',
        'sw_u_exchange', 'sw_u_campaign', 'sw_u_coupon', 'billing_day', 'deleted_at',
        'create_time', 'updated_at'
    ];
    // 隱藏不顯示欄位
    protected $hidden = [];
    // 軟刪除
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'updated_at';

    public function company()
    {
        return $this->belongsTo('App\Model\idelivery\Company');
    }

    public function item()
    {
        return $this->belongsToMany('App\Model\idelivery\MenuItem', 'menu_store_item', 'store_id', 'item_id');       
    }


    /**
     * 關連外送條件資料表 1 : 1//1 : n
     */
    //public function deliveryConditions()
    public function deliveryconditions()
    {
        //return $this->hasMany('App\Model\idelivery\StoreDeliveryCondition', 'store_id');
        return $this->hasOne('App\Model\idelivery\StoreDeliveryCondition', 'store_id', 'id');
    }


    /**
     * 關連營業時間資料表 1 : n
     */
    //public function businessHours()
    public function businesshours()
    {
        return $this->hasMany('App\Model\idelivery\CompanyStoreBusinessHours', 'store_id')->where('type', '=', '1');
    }


    /**
     * 關連營業時間資料表 1 : n
     */
    //public function orderHours()
    public function orderhours()
    {
        return $this->hasMany('App\Model\idelivery\CompanyStoreBusinessHours', 'store_id')->where('type', '=', '2');
    }


    /**
     * 取得上線店家數
     * 
     * @var company_id 公司編號
     */
    public static function getCount($company_id)
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
    public static function getVipCount($company_id)
    {
        return DB::table('store')
                    ->where(array(['status', '1'],['company_id', $company_id],['is_cooperation', '1']))
                    ->count();
    }


    /**
     * 取得店家資料
     * 
     * @var store_id 店家編號
     */
    public static function getData($store_id)
    {
        return DB::table('store')
                    ->where(array(['id', $store_id],['status', '1']))->first();
    }


    /**
     * 取得除 store_id 外之公司所屬店家名稱
     * 
     * @var company_id  公司編號
     * @var store_id    欲排除之店家編號陣列
     */
    public static function getStore($company_id, $store_id = null)
    {
        if (!empty($store_id))
        {
            return DB::table('store')->select('id', 'name')
                    ->where(array(['company_id', $company_id],['status', '1']))
                    ->whereNotIn('id', $store_id)->get();
        }
        else
        {
            return DB::table('store')->select('id', 'name')
                    ->where(array(['company_id', $company_id],['status', '1']))->get();
        }
    }

}
