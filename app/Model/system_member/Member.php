<?php

namespace App\Model\system_member;

use Illuminate\Database\Eloquent\Model;
use DB;

class Member extends Model
{
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'member';
    // 主鍵欄位
    //protected $primaryKey = 'id';
    // 主鍵型態
    //protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'country', 'account'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = ['password'];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    //protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public function detail()
    {
        return $this->hasMany('App\Model\system_member\MemberDetail', 'member_id', 'id');
    }

    public static function getID($account, $country)
    {
        if (empty($country))
        {
            $country = "886";
        }

        $result = self::where('country', $country)
            ->where('account', $account)
            ->select('id')
            ->first();

            return $result;
    }

    public static function getDetailID($service_id, $company_id, $member_id)
    {
        $result = DB::connection('system_member')->table('member_detail')
            ->where('service_id', $service_id)
            ->where('company_id', $company_id)
            ->where('member_id', $member_id)
            ->select('id')
            ->first();

        return $result;
    }


}
