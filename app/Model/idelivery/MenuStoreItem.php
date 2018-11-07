<?php

namespace App\Model\idelivery;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Database\Eloquent\Model;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class MenuStoreItem extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'menu_store_item';
    // 主鍵欄位
    protected $primaryKey = 'item_id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'store_id', 'item_id', 'status', 'sort_by'
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
    // 排序模組
    use SortableTrait;
    public $sortable = [
        'order_column_name' => 'sort_by',
        'sort_when_creating' => true,
    ];

    public static function grid($callback)
    {
        return new Grid(new static, $callback);
    }

    public static function form($callback)
    {
        return new Form(new static, $callback);
    }


    public static function getStoreItem($store_id)
    {
        $result = null;
        $result = MenuStoreItem::join('menu_item', 'menu_item.id', '=', 'menu_store_item.item_id')
                    ->join('cuisine_group', 'menu_item.group_id', '=', 'cuisine_group.id')
                    ->select('menu_store_item.*', 'menu_item.name', 'menu_item.group_id', 
                                'menu_item.picture', 'menu_item.spec_relation')
                    ->where('menu_store_item.store_id', $store_id)
                    ->where('menu_item.status', '1')
                    ->orderBy('menu_item.group_id', 'asc')
                    ->orderBy('menu_store_item.sort_by', 'asc');

        return !empty($result) ? $result : '';
    }


    public static function getMenuSize($item_id)
    {
        $result = null;
        $result = MenuSize::where('menu_size.item_id', $item_id)->get();

        return !empty($result) ? $result : '';
    }


    public static function getItemUnit($item_id)
    {
        $result = null;
        $result = MenuStoreItem::join('menu_item_unit', 'menu_item_unit.item_id', '=', 'menu_store_item.item_id')
                    ->join('cuisine_unit', 'cuisine_unit.id', '=', 'menu_item_unit.unit_id')
                    ->select('cuisine_unit.unit_name')
                    ->where('menu_store_item.item_id', $item_id)->get();

        return !empty($result) ? $result : '';
    }
}
