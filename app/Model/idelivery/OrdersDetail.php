<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use DB;

class OrdersDetail extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'order_detail';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'order_id', 'purchaser_uuid', 'purchaser_name', 'operator_admin_id', 'item_id', 
        'item_name', 'item_optional', 'item_price', 'qty', 'sub_price',
        'create_time'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = false;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    //const CREATED_AT = 'create_time';
    //const UPDATED_AT = 'updated_at';

    public function orders()
    {
        return $this->belongsTo(Orders::class);
    }


    public static function getOrders($order_id)
    {
        $result = DB::table('order')->where('id', '=', $order_id)
                    ->orderBy('sn', 'asc')->first();

        if ($result == false)
        {
            throw new Exception('Data not found');
        }

        return $result;
    }
}
