<?php

namespace App\Admin\Controllers\Company\Exchanges;

use Illuminate\Support\Facades\Session;

class ItemController extends BaseController
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $this->service_id = env('SERVICE_ID');
            $this->company_id = Session::get('company_id');
            $this->store_id   = 0;
            $this->exchanges_type = 1;
            $this->config['index']['description'] = '兌換商品設定'; // 清單描述

            return $next($request);
        });
    }

}
