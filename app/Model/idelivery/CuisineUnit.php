<?php

namespace App\Model\idelivery;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Database\Eloquent\Model;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CuisineUnit extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'cuisine_unit';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'company_id', 'store_id', 'unit_name', 'is_multiple', 'is_required', 
        'sort_by'
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
        return $this->belongsToMany('App\Model\idelivery\CuisineGroup', 'cuisine_unit_group', 'unit_id', 'group_id');        
    }

    //名稱務必要小寫
    public function attrs()
    {
        return $this->hasMany('App\Model\idelivery\CuisineAttr', 'unit_id', 'id');
    }


    //名稱務必要小寫
    public function item()
    {
        return $this->belongsToMany('App\Model\idelivery\MenuItem', 'menu_item_unit', 'unit_id', 'item_id');        
    }


    public static function getGroupUnit($company_id, $store_id = null)
    {
        $result = DB::table('cuisine_group')
                    ->join('cuisine_unit_group', 'cuisine_unit_group.group_id', '=', 'cuisine_group.id')
                    ->join('cuisine_unit', 'cuisine_unit.id', '=', 'cuisine_unit_group.unit_id')
                    ->select('cuisine_unit.id', 'cuisine_unit.unit_name')
                    ->where('cuisine_group.company_id', $company_id)
                    ->where('cuisine_group.store_id', 0)
                    ->orWhere(function ($query) use ($company_id, $store_id) {
                        $query->where('cuisine_group.company_id', '=', $company_id)
                            ->where('cuisine_group.store_id', '=', $store_id);
                    })->get();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        $unit = array();

        foreach (json_decode($result, true) as $rows)
        {
            $unit[$rows['id']] = $rows['unit_name'];
        }

        return !empty($unit) ? $unit : '';
    }

    public static function deleteData($id)
    {
        $status = null;
        $status = DB::table('cuisine_unit_group')->where('unit_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('cuisine_attr')->where('unit_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('cuisine_unit')->where('id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }
    }
}

?>