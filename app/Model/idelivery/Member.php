<?php

namespace App\Model\idelivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Member extends Model
{
    /**
     * 取得會員數
     * 
     * @var company_id 公司編號
     */
    public static function getCount($company_id)
    {
        return DB::table('member')
                    ->where(array(['status', '1'],['company_id', $company_id]))
                    ->count();
    }
}
