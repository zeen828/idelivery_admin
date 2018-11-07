<?php

namespace App\Model\System;

use Illuminate\Database\Eloquent\Model;
use DB;
use Carbon\Carbon;

class Member extends Model
{    
    // 指定資料庫連線名稱
    protected $connection = 'system_member';
    // 資料庫名稱
    protected $table = 'member';
    // 主鍵欄位
    protected $primaryKey = 'id';
    // 主鍵型態
    protected $keyType = 'int';
    // 欄位名稱
    protected $fillable = [
        'country', 'account'
    ];
    // 隱藏不顯示欄位
    //protected $hidden = [];
    // 軟刪除
    //use SoftDeletes;
    // 是否自動待時間撮
    public $timestamps = true;
    // 時間撮保存格式
    protected $dateFormat = 'U';
    // 自訂時間撮欄位
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    /**
     * 取得會員資料
     *
     * @param account       = 會員帳號
     * @param service_id    = 服務編號
     * @param company_id    = 公司編號
     * @param usage         = 依指定欄位查詢
     */
    public static function getView($account, $service_id, $company_id, $usage = 'account')
    {
        $result = self::leftJoin('member_detail', 'member_detail.member_id', '=', 'member.id')
            ->where('member.account', $account)
            ->where('member.country', '886')
            ->where('member_detail.service_id', $service_id)
            ->where('member_detail.company_id', $company_id)
            ->select('member.id as member_id', 'member.account', 'member_detail.*')
            ->get();

        return $result;
    }


    /**
     * 新增點數時, 加入未註冊會員資料
     * 
     * @param account       = [必填] 會員帳號(行動電話)
     * @param service_id    = [必填] 服務編號
     * @param company_id    = [必填] 公司編號
     * @param country       = [選填] 國碼
     * 
     * return TRUE = 成功 OR FALSE = 失敗
     */
	public static function MemberRegister($account, $service_id, $company_id, $country = null)
	{
        $country_code = empty($country) ? '886' : $country;

        $result = DB::connection('system_member')
            ->insert('INSERT INTO member (country, account, created_at) VALUES(?, ?, ?) 
            ON DUPLICATE KEY UPDATE updated_at = ?', [$country_code, $account, Carbon::now(), Carbon::now()]);

        $member_id = DB::connection('system_member')->getPdo()->lastInsertId();
        $uuid = self::createUUID($account);

        $result = DB::connection('system_member')
            ->insert('INSERT INTO member_detail (service_id, member_id, company_id, uuid, created_at) 
            VALUES(?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE updated_at = ?', 
            [$service_id, $member_id, $company_id, $uuid, Carbon::now(), Carbon::now()]);

        return $result;
    }


    /**
     * 產生會員唯一識別碼
     * 
     * @param account = [必填] 帳號 
     */ 
    public static function createUUID($account)
    {
        if(empty($account) == true || isset($account) == false)
        {
            throw new \Exception('參數不正確', 100);
        }

        return hash('sha256', md5(uniqid(null, true).env("TOKEN_KEY").$account));
    }

}
