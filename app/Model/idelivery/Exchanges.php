<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Model\idelivery\Warehouse;
use App\Model\idelivery\Inventory;
use Carbon\Carbon;

class Exchanges extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'exchanges';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'company_id', 'store_id', 'start_date', 'end_date', 
        'name', 'description', 'image', 'point_type_id', 'point', 
        'stock', 'status'
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

//    public function inventory()
//    {
//        return $this->hasOne('App\Model\idelivery\Inventory', 'stock_id', 'id');
//    }

    //取得指定商品兌換資料
    public static function getView($id)
    {
        $now = Carbon::now();

        return self::where('id', $id)->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)->where('status', '1')
            ->get();
    }


    //取得商品兌換資料
    public function getList($service_id, $company_id, $store_id = 0, $type = '1')
    {
        return self::where('service_id', $service_id)
            ->where('company_id', $company_id)
            ->where('store_id', $store_id)
            ->orderBy('id', 'asc');

        // return self::leftJoin('warehouse', function($join)
        //     {
        //         $join->on('warehouse.service_id', '=', 'exchanges.service_id');
        //         $join->on('warehouse.company_id', '=', 'exchanges.company_id');
        //         $join->on('warehouse.store_id', '=', 'exchanges.store_id');
        //     })
        //     ->leftJoin('inventory', function($join2)
        //     {
        //         $join2->on('inventory.stock_id', '=', 'exchanges.id');
        //         $join2->on('inventory.warehouse_id', '=', 'warehouse.id');
        //     })
        //     ->where('exchanges.service_id', $service_id)
        //     ->where('exchanges.company_id', $company_id)
        //     ->where('exchanges.store_id', $store_id)
        //     ->where('inventory.type', $type)
        //     ->select('exchanges.start_date', 'exchanges.end_date', 'exchanges.name', 'exchanges.description',
        //         'exchanges.image', 'exchanges.point_type_id', 'exchanges.point', 'exchanges.status',
        //         'inventory.current_qty', 'exchanges.id')
        //     ->groupBy('exchanges.start_date', 'exchanges.end_date', 'exchanges.name', 'exchanges.description',
        //         'exchanges.image', 'exchanges.point_type_id', 'exchanges.point', 'exchanges.status',
        //         'inventory.current_qty', 'exchanges.id')
        //     ->orderBy('exchanges.id', 'asc');

    }

//    //取得指定兌換商品之庫存數量
//    public static function getInventoryQty($service_id, $company_id, $store_id = 0, $exchanges_id, $type = '1')
//    {
//        $warehouse = Warehouse::where('service_id', $service_id)
//            ->where('company_id', $company_id)
//            ->where('store_id', $store_id)
//            ->select('id')
//            ->get();
//
//        $warehouse_array = array();
//        if (!empty($warehouse))
//        {
//            $warehouse_array = json_decode($warehouse, true);
//        }
//
//        $inventory = Inventory::whereIn('warehouse_id', $warehouse_array)
//            ->where('type', $type)
//            ->where('stock_id', $exchanges_id)
//            ->select('current_qty')
//            ->first();
//
//        if (!empty($inventory))
//        {
//            return $inventory->current_qty;
//        }
//        else
//        {
//            return 0;
//        }
//    }


    //取得指定兌換商品之庫存編號
//    public static function getInventoryID($service_id, $company_id, $store_id = 0, $exchanges_id, $type = '1')
//    {
//        $warehouse = Warehouse::where('service_id', $service_id)
//            ->where('company_id', $company_id)
//            ->where('store_id', $store_id)
//            ->select('id')
//            ->get();
//
//        $warehouse_array = array();
//        if (!empty($warehouse))
//        {
//            $warehouse_array = json_decode($warehouse, true);
//        }
//
//        $inventory = Inventory::whereIn('warehouse_id', $warehouse_array)
//            ->where('type', $type)
//            ->where('stock_id', $exchanges_id)
//            ->select('id')
//            ->first();
//
//        if (!empty($inventory))
//        {
//            return $inventory->id;
//        }
//        else
//        {
//            return 0;
//        }
//
//    }


    //刪除兌換商品資訊
    public static function deleteData($id)
    {
        return self::where('id', $id)->delete();

    }

}
