<?php

namespace App\Admin\Controllers\System;

use App\Http\Controllers\Controller;
use App\Model\idelivery\Admin_users;
use Encore\Admin\Facades\Admin;
use Encore\Admin\Controllers\ModelForm;
use Illuminate\Support\Facades\Session;

class SystemController extends Controller
{
    use ModelForm;

    /**
     * 如果所屬總公司有直就跳回[儀表板]
     * 沒有的話要是只登記一間店家自動幫你選擇
     * 以加以上就只做[儀表板]跳轉
     */
    public function autoLoadSession()
    {
        $confirm_id = false;
        $default_company_id = 0;
        $default_store_id = 0;
        $company_id = Session::get('company_id');
        $store_id = Session::get('store_id');
        $login_id = Admin::user()->id;
        $user = Admin_users::find($login_id);
        if (count($user->store) >= 1){
            foreach ($user->store as $key=>$store){
                // 第一筆當預設記錄用資料
                if($key == 0){
                    $default_company_id = $store->company->id;
                    $default_store_id = $store->id;
                }
                // 檢查紀錄是否屬於使用者
                if($company_id == $store->company->id && $store_id == $store->id){
                    $confirm_id = true;
                }
            }
        }
        // 現在紀錄不屬於你將第一組預設紀錄
        if(empty($confirm_id)){
            // 銷毀SESSION
            //Session::flush();// 全部銷毀會連登入資料都銷毀
            Session::forget('company_id');
            Session::forget('store_id');
        }
        // 如果只有一筆自動帶預設,多筆請他自己選擇
        if(count($user->store) == 1){
            Session::put('company_id', $default_company_id);
            Session::put('store_id', $default_store_id);
        }
        return redirect('/admin/dashboard');
    }
}
