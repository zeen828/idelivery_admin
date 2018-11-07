<?php

namespace App\Model\idelivery;


use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Database\Eloquent\Model;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class CuisineGroup extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'cuisine_group';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'company_id', 'store_id', 'group_name', 'status', 'sort_by'
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
    public function category()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineCategory', 'cuisine_group_category', 'group_id', 'category_id');        
    }


    public function type()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineType', 'cuisine_group_type', 'group_id', 'type_id');        
    }


    public function unit()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineUnit', 'cuisine_unit_group', 'group_id', 'unit_id');        
    }

    public function menuitem()
    {
        return $this->hasMany('App\Model\idelivery\MenuItem', 'group_id', 'id');
    }


    public static function getGroup($company_id, $store_id = null)
    {
        $result = DB::table('cuisine_group')->select('id', 'group_name')
                        ->where('company_id', $company_id)
                        ->where('store_id', 0)
                        ->orWhere(function ($query) use ($company_id, $store_id) {
                            $query->where('company_id', $company_id)
                                  ->where('store_id', $store_id);
                        })->get();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        $groups = array();

        foreach (json_decode($result, true) as $rows)
        {
            $groups[$rows['id']] = $rows['group_name'];
        }

        return !empty($groups) ? $groups : '';
    }


    public static function getGroupName($id)
    {
        $result = DB::table('cuisine_group')->select('group_name')
                    ->where('id', '=', $id)->first();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }
       
        return !empty($result) ? $result->group_name : '';
    }


    public static function deleteData($id)
    {
        $status = null;

        $status = DB::table('cuisine_group_category')->where('group_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('cuisine_group_type')->where('group_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('cuisine_unit_group')->where('group_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('cuisine_group')->where('id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        return $status;
    }

}

?>