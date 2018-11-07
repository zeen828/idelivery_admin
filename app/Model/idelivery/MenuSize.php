<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class MenuSize extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'menu_size';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'size_name', 'price', 'is_selected', 'status'
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

    public function item()
    {
        return $this->belongsTo('App\Model\idelivery\MenuItem', 'item_id', 'id'); 
    }



    // public static function newID()
    // {
    //     $result = DB::table('menu_items_size')
    //                 ->select('id')
    //                 ->orderby('id', 'desc')
    //                 ->first();

    //     if ($result === false)
    //     {
    //         throw new \Exception('資料擷取錯誤');
    //     }

    //     return isset($result) ? $result->id + 1 : 1;
    // }


    // public static function insertMenuItemsSize($data)
    // {
    //     if (!empty($data))
    //     {
    //         for ($i = 0; $i < count($data); $i++)
    //         {
    //             $result = DB::table('menu_items_size')
    //                         ->insert($data[$i]);

    //             if ($result === false)
    //             {
    //                 throw new \Exception('資料新增錯誤');
    //             }
    //         }
    //     }

    // }


    // public static function updateMenuItemsSize($data)
    // {
    //     if (!empty($data))
    //     {
    //         foreach ($data as $key => $val)
    //         {
    //             $result = DB::table('menu_items_size')
    //                         ->where('id', $key)
    //                         ->update($val[0]);

    //             if ($result === false)
    //             {
    //                 throw new \Eexception('資料更新錯誤');
    //             }
            
    //         }

    //     }

    // }
}
