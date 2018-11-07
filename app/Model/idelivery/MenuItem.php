<?php

namespace App\Model\idelivery;

use Encore\Admin\Form;
use Encore\Admin\Grid;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Model\idelivery\MenuSize;
use DB;

use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class MenuItem extends Model implements Sortable
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'menu_item';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'company_id', 'store_id', 'name', 'intro', 'picture',
        'group_id', 'spec_relation', 'status', 'hidden', 'sort_by'
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

    public function cuisine_group()
    {
        return $this->hasOne('App\Model\idelivery\CuisineGroup', 'id', 'group_id');
    }

    public function menu_size()
    {
        return $this->hasMany('App\Model\idelivery\MenuSize', 'id', 'item_id');
    }

    public static function grid($callback)
    {
        return new Grid(new static, $callback);
    }

    public static function form($callback)
    {
        return new Form(new static, $callback);
    }

    public function size()
    {
        return $this->hasMany('App\Model\idelivery\MenuSize', 'item_id', 'id');        
    }

    public function unit()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineUnit', 'menu_item_unit', 'item_id', 'unit_id');        
    }

    public function group()
    {
        return $this->belongsToMany('App\Model\idelivery\CuisineGroup', 'menu_item_unit', 'item_id', 'unit_id');        
    }

    public function store()
    {
        return $this->belongsToMany('App\Model\idelivery\Store', 'menu_store_item', 'item_id', 'store_id');       
    }


    public static function getGroupID($id)
    {
        $result = DB::table('menu_item')->select('group_id')->where('id', '=', $id)
                    ->first();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result->group_id : '';
    }


    public static function getAssignGroupOption($group_id)
    {
        $result = DB::table('cuisine_group')->select('group_name')
                    ->where('id', '=', $group_id)
                    ->first();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }
        
        return !empty($result) ? $result->group_name : '';
    }


    public static function getUnit($company_id)
    {
        $result = null;
        $result = DB::table('cuisine_unit')
                    ->select('cuisine_unit.*')
                    ->join('cuisine_unit_group', 'cuisine_unit_group.unit_id', '=', 'cuisine_unit.id')
                    ->join('cuisine_group', 'cuisine_group.id', '=', 'cuisine_unit_group.group_id')
                    ->where('cuisine_group.company_id', '=', $company_id)
                    ->orderby('cuisine_unit.id')
                    ->distinct()
                    ->get();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }


    public static function getAttr($unit_id_list)
    {
        $result = null;
        $result = DB::table('cuisine_attr')
                    ->select('cuisine_attr.*')
                    ->whereIn('cuisine_attr.unit_id', $unit_id_list)
                    ->get();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }


    public static function getSizeAndPrice($item_id)
    {
        $result = null;
        $result = MenuSize::where('item_id', $item_id)
                    ->select('size_name', 'price')
                    ->where('status', '1')
                    ->orderBy('price', 'desc')
                    ->first();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : null;
    }


    public static function getStoreItemId($store_id)
    {
        $result = null;
        $result = DB::table('menu_store_item')
                    ->select('item_id')
                    ->where('store_id', $store_id)
                    ->get();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }


    public static function updateSpecRelation($id, $data)
    {
        $result = null;
        $result = DB::table('menu_item')
                    ->where('id', '=', $id)
                    ->update(['spec_relation' => $data]);

        // if ($result === false)
        // {
        //     throw new \Exception('資料更新錯誤 !');
        // }

        return $result;
    }



    // public static function newID()
    // {
    //     $result = DB::table('menu_item')
    //                 ->select('id')
    //                 ->orderby('id', 'desc')
    //                 ->first();

    //     if ($result === false)
    //     {
    //         throw new exception('資料擷取錯誤');
    //     }

    //     return isset($result) ? $result->id + 1 : 1;
    // }

    public static function getSpecRelation($id)
    {
        $result = null;
        $result = DB::table('menu_item')
                    ->select('spec_relation')
                    ->where('id', $id)
                    ->first();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result->spec_relation : '';
    }

    public static function deleteData($id)
    {
        $status = null;
        $status = DB::table('menu_store_item')->where('item_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = null;
        $status = DB::table('menu_item_unit')->where('item_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('menu_size')->where('item_id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }

        $status = DB::table('menu_item')->where('id', $id)->delete();
        if ($status === false)
        {
            throw new \Exception('資料刪除錯誤 !');
        }
        return $status;
    }


    public static function addMenuItemUnit($item_id, $data)
    {
        $status = null;

        if (!empty($item_id))
        {
            $status = DB::table('menu_item_unit')->where('item_id', '=', $item_id)->delete();
            if ($status === false)
            {
                throw new \Exception('資料刪除錯誤 !');
            }
        }

        if (!empty($data))
        {           
            $status = DB::table('menu_item_unit')->insert($data);
            if ($status === false)
            {
                throw new \Exception('資料新增錯誤 !');
            }
        }

        return $status;
    }


    // public static function updatePrice($ids, $prices)
    // {
    //     $status = null;
    //     for ($i = 0; $i < count($ids); $i++)
    //     {
    //         $status = DB::table('menu_size')->where('id', $ids[$i])
    //                     ->update(['price' => $prices[$i]]);

    //         if ($status === false)
    //         {
    //             throw new \Exception('資料修改錯誤 !');
    //         }
    //     }

    //     return $status;
    // }


    public static function updateSizeName($size_ids, $size_names)
    {
        $status = true;
        for ($i = 0; $i < count($size_ids); $i++)
        {
            $status = DB::table('menu_size')->where('id', $size_ids[$i])
                        ->update(['size_name' => $size_names[$i]]);

            if ($status === false)
            {
                throw new \Exception('資料修改錯誤 !');
            }
        }

        return $status;
    }



    public static function getMenuItem($store_id)
    {
        $result = MenuItem::join('menu_store_item', 'menu_store_item.item_id', '=', 'menu_item.id')
                        ->select('menu_item.*', 'menu_store_item.status as store_item_status')
                        ->where('menu_store_item.store_id', '=', $store_id)
                        ->orderBy('menu_item.sort_by', 'asc')
                        ->get();
                        

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }

    public static function getGroupCompanyMenuItem($company_id, $group_id)
    {
        $result = MenuItem::where('company_id', '=', $company_id)
                        ->where('store_id', '=', 0)
                        ->where('group_id', '=', $group_id)
                        ->orderBy('sort_by', 'asc')
                        ->get();
                        

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }


    public static function getGroupStoreMenuItem($store_id, $group_id)
    {
        $result = MenuItem::join('menu_store_item', 'menu_store_item.item_id', '=', 'menu_item.id')
                        ->select('menu_item.*', 'menu_store_item.status as store_item_status')
                        ->where('menu_store_item.store_id', '=', $store_id)
                        ->where('menu_item.group_id', '=', $group_id)
                        ->orderBy('menu_item.sort_by', 'asc')
                        ->get();
                        

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }



    public function getStoreItem($store_id)
    {
        $result = $this->rightJoin('menu_store_item', 'menu_store_item.item_id', '=', 'menu_item.id')
                    ->select('menu_item.*')
                    ->where('menu_store_item.store_id', '=', $store_id)
                    ->orderBy('menu_store_item.sort_by', 'asc');

        if ($result === false)
        {
        throw new \Exception('資料擷取錯誤 !');
        }

        return $result;
    }


    public static function addMenuStoreItem($store_id, $item_id)
    {
        $status = null;
        if (!empty($store_id) && !empty($item_id))
        {
            $sn = DB::table('menu_store_item')->select('sort_by')->where('store_id', $store_id)
                        ->orderBy('sort_by', 'desc')->first();
            if ($sn === false)
            {
                throw new \Exception('資料擷取錯誤 !');
            }

            if (!isset($sn))
            {
                $sort_by = 1;
            }
            else
            {
                $sort_by = $sn->sort_by + 1;
            }
            
            $now = date('Y-m-d H:i:s');               

            $result = DB::insert('insert into menu_store_item (store_id, item_id, status, sort_by, created_at) 
                values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = NOW()', [$store_id, $item_id, '2', $sort_by, $now]);
                
            if ($status === false)
            {
                throw new \Exception('資料新增錯誤 !');
            }
        }

        return $status;
    }


    public static function getCuisineGroup($company_id, $store_id)
    {
        $result = null;
        if (!empty($store_id))
        {
            $result = DB::table('cuisine_group')
                        ->select('id', 'group_name')
                        ->where('store_id', '=', $store_id)
                        ->orWhere(function ($query) use ($company_id) {
                            $query->where('company_id', '=', $company_id)
                                ->where('status', '=', '1')
                                ->where('store_id', 0);
                        })->get();
            if ($result === false)
            {
                throw new \Exception('資料擷取錯誤 !');
            }
        }

        return !empty($result) ? $result : '';
    }

    public static function importMenu($company_id, $store_id)
    {
        $result = null;
        if (!empty($company_id) && !empty($store_id))
        {
            $result = DB::table('menu_item')->select('id', 'sort_by')->where('company_id', '=', $company_id)
                            ->where('status', '=', '1')->where('store_id', 0)->get();
            
            if ($result === false)
            {
                throw new \Exception('資料擷取錯誤 !');
            }

            $data = array();
            $id_set = array();

            if (!empty($result))
            {
                foreach ($result as $val)
                {
                    $now = date('Y-m-d H:i:s');
                    $id_set[] = $val->id;
                    $data[] = array(
                        'store_id' => $store_id,
                        'item_id' => $val->id,
                        'status' => '1',
                        'sort_by' => $val->sort_by,
                        'created_at' => $now
                    );
                }
    
                $result = DB::table('menu_store_item')->whereIn('item_id', $id_set)->delete();
                if ($result === false)
                {
                    throw new \Exception('資料刪除錯誤 !');
                }
    
                $result = DB::table('menu_store_item')->insert($data);
                if ($result === false)
                {
                    throw new \Exception('資料新增錯誤 !');
                }
            }
        }

        return $result;
    }


    public static function getMenuItemStore($item_id)
    {
        $result = null;
        if (!empty($item_id))
        {
            $result = DB::table('menu_store_item')
                        ->select('store.name')
                        ->join('store', 'menu_store_item.store_id', '=', 'store.id')
                        ->where('menu_store_item.item_id', '=', $item_id)
                        ->get();

            if ($result === false)
            {
                throw new \Exception('資料擷取錯誤 !');
            }
        }

        return !empty($result) ? $result : '';
    }

    public static function AddStoreMenuItem($store_id, $item_id)
    {
        $result = null;

        if (!empty($store_id) && !empty($item_id))
        {
            foreach ($store_id as $store)
            {
                $sn = DB::table('menu_store_item')->select('sort_by')->where('store_id', $store)
                        ->orderBy('sort_by', 'desc')->first();
                if ($sn === false)
                {
                    throw new \Exception('資料擷取錯誤 !');
                }

                if (!isset($sn))
                {
                    $sort_by = 1;
                }
                else
                {
                    $sort_by = $sn->sort_by + 1;
                }
                
                $now = date('Y-m-d H:i:s');               
                $result = DB::insert('insert into menu_store_item (store_id, item_id, status, sort_by, created_at) 
                                values (?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = NOW()', [$store, $item_id, '2', $sort_by, $now]);
                
                if ($result === false)
                {
                    throw new \Exception('資料新增錯誤 !');
                }
            }
        }
        return $result;
    }


    public static function getCompanyMenuItem($company_id)
    {
        $result = DB::table('menu_item')->select('id')
                    ->where('company_id', $company_id)
                    ->where('store_id', '=', 0)
                    ->get();

        if ($result === false)
        {
            throw new \Exception('資料擷取錯誤 !');
        }

        return !empty($result) ? $result : '';
    }


    public static function AddMenuItem2Store($company_id, $store_id)
    {
        $result = true;

        if (!empty($company_id) && !empty($store_id))
        {
            $menu_item = MenuItem::getCompanyMenuItem($company_id);

            if (!empty($menu_item))
            {
                foreach ($store_id as $store)
                {
                    foreach ($menu_item as $item)
                    {
                        $sn = DB::table('menu_store_item')->select('sort_by')->where('store_id', $store)
                                ->orderBy('sort_by', 'desc')->first();
                        if ($sn === false)
                        {
                            throw new \Exception('資料擷取錯誤 !');
                        }

                        if (!isset($sn))
                        {
                            $sort_by = 1;
                        }
                        else
                        {
                            $sort_by = $sn->sort_by + 1;
                        }
                        
                        $now = date('Y-m-d H:i:s');               
                        $result = DB::insert('insert into menu_store_item (store_id, item_id, sort_by, created_at) 
                                        values (?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = NOW()', [$store, $item->id, $sort_by, $now]);
                        
                        if ($result === false)
                        {
                            throw new \Exception('資料新增錯誤 !');
                        }
                    }
                }
            }
        }
        return $result;
    }


    public static function exportMenu($company_id, $store_id)
    {
        $result = null;
        if (!empty($company_id) && !empty($store_id))
        {
            $result = DB::table('menu_item')->select('id', 'status', 'sort_by')->where('company_id', '=', $company_id)
                            ->where('status', '=', '1')->whereNull('store_id')->get();
            
            if ($result === false)
            {
                throw new \Exception('資料擷取錯誤 !');
            }

            $data = array();
            $id_set = array();

            if (!empty($result))
            {
                foreach ($result as $val)
                {
                    $now = date('Y-m-d H:i:s');
                    $id_set[] = $val->id;
                    $data[] = array(
                        'store_id' => $store_id,
                        'item_id' => $val->id,
                        'status' => $val->status,
                        'sort_by' => $val->sort_by,
                        'created_at' => $now
                    );
                }
    
                $result = DB::table('menu_store_item')->whereIn('item_id', $id_set)->delete();
                if ($result === false)
                {
                    throw new \Exception('資料刪除錯誤 !');
                }
    
                $result = DB::table('menu_store_item')->insert($data);
                if ($result === false)
                {
                    throw new \Exception('資料新增錯誤 !');
                }
            }
        }

        return $result;
    }
}
