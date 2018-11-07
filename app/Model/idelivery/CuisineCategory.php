<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CuisineCategory extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'cuisine_category';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'name', 'status', 'sort_by'
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
    public function group()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineGroup', 'cuisine_group_category', 'category_id', 'group_id');        
    }

    // public static function getName()
    // {
    //     $result = DB::table('cuisine_category')->select('id', 'name')
    //                 ->where('status', '1')->orderBy('sort_by', 'asc')->get();

    //     if ($result === false)
    //     {
    //         throw new \Exception('資料擷取發生錯誤');
    //     }

    //     return $result;
    // }

}

?>