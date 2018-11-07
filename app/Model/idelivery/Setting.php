<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;

class Setting extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'idelivery';
    // 資料庫名稱
    protected $table = 'setting';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'id', 'store_id', 'type', 'sn', 'status', 'title', 'content'
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


    public static function getList($store_id, $type)
    {
       $result = DB::table('setting')->where('store_id', '=', $store_id)->where('type', '=', $type)->get();

       return $result;
    }

    public static function getContent($store_id, $type)
    {
        $result = DB::table('setting')->where('store_id', '=', $store_id)->where('type', '=', $type)
                    ->select('content')->get();
        
        return $result;
    }

    public static function post($store_id, $type, $content = null)
    {
        $result = DB::table('setting')->where('store_id', '=', $store_id)->where('type', '=', $type)->get();

        if ($result === false)
        {
            throw new ModelException("取得資料發生錯誤", 400500);
        }

        if (empty($result))
        {
            self::insert($store_id, $type, $content);
        }
        else
        {
            self::change($store_id, $type, $content);
        }
    }

    public static function insert($store_id, $type, $content)
    {
        $result = DB::table('setting')->insert(
                        ['store_id' => $store_id, 'type' => $type, 'content' => $content]
                    );

        if ($result === false)
        {
            throw new ModelException("新增資料發生錯誤", 400500);
        }
    }

    public static function change($store_id, $type, $content)
    {
        $result = DB::table('setting')->where('store_id', '=', $store_id)->where('type', '=', $type)
                    ->update(['content' => $content]);

        if ($result === false)
        {
            throw new ModelException("增修改資料發生錯誤", 400500);
        }
    }
}
