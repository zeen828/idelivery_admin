<?php

namespace App\Admin\Controllers\Store\Exchanges;

use Encore\Admin\Form;
use Illuminate\Support\Facades\Session;

class CouponController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->service_id = env('SERVICE_ID');
            $this->company_id = Session::get('company_id');
            $this->store_id   = Session::get('store_id');;
            $this->exchanges_type = 2;
            $this->config['index']['description'] = '兌換優惠券設定'; // 清單描述

            return $next($request);
        });
    }

    /**
     * Add additional form field
     *
     * @return Form
     */
    protected function addAdditionalFormField(Form $form)
    {
        $service_id     = $this->service_id;
        $company_id     = $this->company_id;
        $store_id       = $this->store_id;
        $exchanges_type = $this->exchanges_type;

        // 取得優惠券活動資料作為表單選項
        $campaign = \App\Models\idelivery\Campaign_setting::all()
                        ->where('company_id', $company_id)
                        ->where('store_id', $store_id)
                        ->where('types', $exchanges_type);
        $options_campaign = [];
        foreach ($campaign as $c){
            $options_campaign[$c['id']] = $c['title'];
        }
        $form->select('campaign_setting_id', '優惠活動')->options($options_campaign);

        return $form;
    }
}
