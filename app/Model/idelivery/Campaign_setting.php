<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign_setting extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'campaign_setting';
    // 主鍵欄位
    // protected $primaryKey = 'id';
    // 主鍵型態
    // protected $keyType = 'int';
    // 白名單適用批量新增或更新的欄位
    // protected $fillable = ['campaign_condition_id', 'campaign_offer_id'];
    // 隱藏不顯示欄位
    // protected $hidden = ['password'];
    // 軟刪除
    use SoftDeletes;
    // 是否自動待時間撮
    // public $timestamps = false;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    // const CREATED_AT = 'create_at';
    // const UPDATED_AT = 'updated_at';
    public function qty_conditions()
    {
        return $this->hasOne('App\Model\idelivery\Campaign_condition_qty', 'setting_id', 'id');
    }

    public function amount_conditions()
    {
        return $this->hasOne('App\Model\idelivery\Campaign_condition_amount', 'setting_id', 'id');
    }

    public function coupon_offers()
    {
        return $this->hasOne('App\Model\idelivery\Campaign_offer_coupon', 'setting_id', 'id');
    }

    public function discount_offers()
    {
        return $this->hasOne('App\Model\idelivery\Campaign_offer_discount', 'setting_id', 'id');
    }

    public function qty_offers()
    {
        return $this->hasOne('App\Model\idelivery\Campaign_offer_qty', 'setting_id', 'id');
    }

    public function amount_offers()
    {
        return $this->hasOne('App\Model\idelivery\Campaign_offer_amount', 'setting_id', 'id');
    }
}