<?php

namespace App\Model\idelivery;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Database\Eloquent\Model;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CuisineAttr extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'cuisine_attr';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'company_id', 'store_id', 'attr_name', 'is_default', 'extra_price', 
        'status', 'sort_by'
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


    //名稱務必要小寫
    public function unit()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineUnit', 'cuisine_unit_attr', 'attr_id', 'unit_id');        
    }

    //名稱務必要小寫
    public function unitattr()
    {
        return $this->belongsTo('App\Model\idelivery\CuisineUnitAttr', 'attr_id', 'id');        
    }

    
    // public static function getNewSN($company_id, $store_id = null)
    // {
    //     $sn = DB::table('cuisine_attr')->where('company_id', $company_id)
    //             ->whereNull('store_id')
    //             ->orWhere(function ($query) use ($company_id, $store_id){
    //                 $query->where('company_id', '=', $company_id)
    //                     ->where('store_id', '=', $store_id);
    //             })->orderBy('sort_by', 'desc')->first();

    //     if ($sn == false)
    //     {
    //         $result = 1;
    //     }
    //     else
    //     {
    //         $result = $sn->sort_by + 1;
    //     }

    //     return $result;
    // }


}

?>