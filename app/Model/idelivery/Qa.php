<?php

namespace App\Model\idelivery;

use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Qa extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'question_answer';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'id', 'store_id', 'type', 'parent_id', 'sn', 'question', 'answer'
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

    use ModelTree, AdminBuilder;

    // public function __construct(array $attributes = [])
    // {
    //     parent::__construct($attributes);

    //     $this->setParentColumn('parent_id');
    //     $this->setOrderColumn('sn');
    //     $this->setTitleColumn('question');
    // }


    public static function grid($callback)
    {
        return new Grid(new static, $callback);
    }

    public static function form($callback)
    {
        return new Form(new static, $callback);
    }

    
    public static function getList($store_id, $type)
    {
        $result = DB::table('question_answer')->where('store_id', '=', $store_id)->where('type', '=', $type)
                    ->orderBy('sn', 'asc')->get();
                    
        
       return $result;
    }

    public static function getNewSN($store_id)
    {
        $sn = DB::table('question_answer')->where('store_id', '=', $store_id)
                    ->orderBy('sn', 'desc')->get();

        if ($sn == false)
        {
            $result = 1;
        }
        else
        {
            $result = $sn[0]->sn + 1;
        }

        return $result;
    }
}
