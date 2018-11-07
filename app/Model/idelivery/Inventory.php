<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Inventory extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'inventory';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'warehouse_id', 'type', 'stock_id', 'opening_date', 'opening_stock', 
        'opening_cost', 'safe_qty', 'current_qty', 'created_at', 'updated_at' 
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
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function exchanges()
    {
        return $this->belongsTo('App\Model\idelivery\Exchanges', 'stock_id', 'id');
    }

    //更新庫存商品數量
    public static function updateQty($id, $qty)
    {
        return Inventory::where('id', $id)
                    ->update(['current_qty' => $qty]);
    }

    //刪除兌換商品庫存資料
    public static function deleteData($stock_id)
    {
        return self::where('type', '1')->where('stock_id', $stock_id)->delete();
    }

    public static function findView($warehouse_id, $stock_id, $type = '1')
    {
        return self::where('warehouse_id', $warehouse_id)
            ->where('stock_id', $stock_id)
            ->where('type', $type)
            ->select('*')
            ->first();
    }

}
