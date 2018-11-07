<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use DB;
use App\Model\idelivery\Warehouse;
use App\Model\system_member\PointConsumeLog;
use App\Model\system_member\Point;
use Carbon\Carbon;

class ProductExchange extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'product_exchange';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'service_id', 'member_id', 'company_id', 'store_id', 'member_detail_id', 
        'date', 'exchanges_id', 'qty', 'point_type_id', 'point_before', 
        'point_after', 'orders_id', 'status', 'created_at', 'updated_at'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';


    public static function getView($id)
    {
        return self::where('id', $id)->first();
    }


    public static function getList($orders_id = 0)
    {
        $result = self::join('exchanges', 'exchanges.id', '=', 'product_exchange.exchanges_id')
            ->where('product_exchange.orders_id', $orders_id)
            ->select('exchanges.name', 'exchanges.image', 'exchanges.point_type_id', 'exchanges.point',
                'product_exchange.qty', DB::raw('exchanges.point * product_exchange.qty as total_point'), 
                'product_exchange.status', 'product_exchange.id')->get(); 

        return $result;
    
    }

    public static function getListByOnSide($service_id, $company_id, $store_id)
    {
        $result = self::join('exchanges', 'exchanges.id', '=', 'product_exchange.exchanges_id')
            ->where('product_exchange.service_id', $service_id)
            ->where('product_exchange.company_id', $company_id)
            ->where('product_exchange.store_id', $store_id)
            ->where('product_exchange.orders_id', 0)
            ->select('product_exchange.id', 'product_exchange.date', 'exchanges.name', 'exchanges.image', 
                'exchanges.point_type_id', 'exchanges.point', 'product_exchange.qty', 
                DB::raw('exchanges.point * product_exchange.qty as total_point'), 
                'product_exchange.status'); 

        return $result;
    
    }


    // public static function getExchangesList($date, $orders_id = 0)
    // {
    //     $result = self::join('exchanges', 'exchanges.id', '=', 'product_exchange.exchanges_id')
    //         ->where('product_exchange.orders_id', $orders_id)
    //         ->where('product_exchange.date', $date)
    //         ->select('exchanges.name', 'exchanges.image', 'exchanges.point_type_id', 'exchanges.point',
    //             'product_exchange.qty', DB::raw('exchanges.point * product_exchange.qty as total_point'))
    //         ->get(); 

    //     return $result;
    // }


    public static function updateStatus($id, $status)
    {
        return self::where('id', $id)
            ->update(['status' => $status]);
    }


    public static function ExchangeRollBack($service_id, $company_id, $store_id, $id)
    {
        $exchange_view = self::getView($id);
        if ($exchange_view === false)
        {
            return false;
        }

        if (empty($exchange_view))
        {
            return false;
        }

        $warehouse = Warehouse::getFirstID($service_id, $company_id, $store_id);
        if (empty($warehouse) || $warehouse === false)
        {
            return false;
        }

        $inventory = Inventory::findView($warehouse, $exchange_view->exchanges_id);
        if (empty($inventory))
        {
            return false;
        }

        $inventory_id = $inventory->id;
        $qty = $exchange_view->qty + $inventory->current_qty;

        $result = DB::transaction(function () use ($exchange_view, $inventory_id, $qty) {
            //加回庫存
            Inventory::updateQty($inventory_id, $qty);

            //取得兌換扣點紀錄
            $point_consume_log = PointConsumeLog::getExchangeView($exchange_view->id);

            if ($point_consume_log->isEmpty())
            {
                return false;
            }

            //更新點數
            foreach ($point_consume_log as $row)
            {
                $args= array(
                    'service_id'        => $exchange_view->service_id,
                    'member_id'         => $exchange_view->member_id,
                    'company_id'        => $exchange_view->company_id,
                    'store_id'          => $exchange_view->store_id,
                    'member_detail_id'  => $exchange_view->member_detail_id,
                    'operating_role'    => 'System',
                    'description'       => '現場兌換取消, 點數回存',
                    'point_type_id'     => $row->point_type_id,
                    'order_id'          => $row->order_id,
                    'point'             => $row->point_deducted,
                    'point_surplus'     => $row->point_deducted,
                    'expired_at'        => $row->expired_at,
                    'created_at'        => Carbon::now()
                );

                $result = Point::addExchangePoint($args);
                if ($result === false)
                {
                    return false;
                }
            }

            return true;
        }, 5);

        return $result;
    }



    public static function OrderExchangeRollBack($service_id, $company_id, $store_id, $id)
    {
        $exchange_view = self::getView($id);
        if ($exchange_view === false)
        {
            return false;
        }

        if (empty($exchange_view))
        {
            return false;
        }

        $warehouse = Warehouse::getFirstID($service_id, $company_id, $store_id);
        if (empty($warehouse) || $warehouse === false)
        {
            return false;
        }

        $inventory = Inventory::findView($warehouse, $exchange_view->exchanges_id);
        if (empty($inventory))
        {
            return false;
        }

        $inventory_id = $inventory->id;
        $qty = $exchange_view->qty + $inventory->current_qty;

        $result = DB::transaction(function () use ($exchange_view, $inventory_id, $qty) {
            //加回庫存
            Inventory::updateQty($inventory_id, $qty);

            //取得兌換扣點紀錄
            $point_consume_log = PointConsumeLog::getExchangeView($exchange_view->id);

            if ($point_consume_log->isEmpty())
            {
                return false;
            }

            $role = Admin::user()->id;
            //更新點數
            foreach ($point_consume_log as $row)
            {
                $args= array(
                    'service_id'        => $exchange_view->service_id,
                    'member_id'         => $exchange_view->member_id,
                    'company_id'        => $exchange_view->company_id,
                    'store_id'          => $exchange_view->store_id,
                    'member_detail_id'  => $exchange_view->member_detail_id,
                    'operating_role'    => $role,
                    'description'       => '訂單兌換取消, 點數回存',
                    'point_type_id'     => $row->point_type_id,
                    'order_id'          => $row->order_id,
                    'point'             => $row->point_deducted,
                    'point_surplus'     => $row->point_deducted,
                    'expired_at'        => $row->expired_at,
                    'created_at'        => Carbon::now()
                );

                $result = Point::addExchangePoint($args);
                if ($result === false)
                {
                    return false;
                }
            }

            return true;
        }, 5);

        return $result;
    }


    public static function add($data)
    {
        return self::insert($data);
    }
}
