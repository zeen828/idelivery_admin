<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;


class OtherSetting extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'other_setting';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'store_id', 'sn', 'reason'
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
    // 排序模組
    use SortableTrait;
    public $sortable = [
        'order_column_name' => 'sn',
        'sort_when_creating' => true,
    ];

    public static function getNewSN($store_id)
    {
        $sn = DB::table('other_setting')->where('store_id', '=', $store_id)
                    ->orderBy('sn', 'desc')->get();

        if ($sn == false || empty($sn))
        {
            $result = 1;
        }
        else
        {
            $result = $sn[0]->sn + 1;
        }

        return $result;
    }

    public static function grid($callback)
    {
        return new Grid(new static, $callback);
    }

    public static function form($callback)
    {
        return new Form(new static, $callback);
    }

}
