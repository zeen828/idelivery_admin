<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 2018/6/3
 * Time: 下午 10:39
 */



return [
    'index'         => '列表',
    'edit'          => '編輯',
    'create'        => '建立',
    'setting_tab'   => '活動設定',
    'time_tab'      => '時間設定',
    'cond_tab'      => '條件設定',
    'offer_tab'     => '優惠設定',
    'field'     => [
        'id'                => '編號',
        'title'             => '活動名稱',
        'description'       => '活動說明',
        'start_at'          => '起始時間',
        'end_at'            => '結束時間',
        'duration'          => '活動期間',
        'after_day'         => '領後有效天數',
        'week'              => '指定星期',
        'remark'            => '備註',
        'max_qty'           => '序號數',
        'used_count'        => '使用次數',
        'user_use_count'    => '可使用次數',
        'max_value'         => '活動最高折抵金額',
        'status'            => '狀態',
        'plural'            => '優惠是否合併使用',
        'repeat'            => '優惠是否可以累計',
        'product_delivery'  => '取餐方式',
        'offer_max_value'   => '最高免費數量',
        'hidden'            => 'App隱藏'
    ],
    'cash'    => [
        'cond_value'       => '滿額金額',
        'amount' => [
            'header'            => '現金抵用券',
            'offer_value'       => '折抵金額',
        ],
        'discount' => [
            'header'            => '現金折扣券',
            'offer_value'       => '打折比例',
        ],
        'qty' => [
            'header'            => 'Y件免費抵用券',
            'offer_value'       => '贈送數量',
        ],
    ],
    'amount'    => [
        'cond_value'       => '滿額金額',
        'amount' => [
            'header'            => '滿額現金抵用券',
            'offer_value'       => '折抵金額',
            //'offer_max_value'   => '最高折抵金額',
        ],
        'discount' => [
            'header'            => '滿額打折抵用券',
            'offer_value'       => '打折比例',
        ],
        'qty' => [
            'header'            => '滿額Y件免費抵用券',
            'offer_value'       => '贈送數量',
        ],
    ],
    'qty'    => [
        'cond_value'       => '滿件數量',
        'amount' => [
            'header'            => '滿X件現金抵用券',
            'offer_value'       => '折抵金額',
        ],
        'discount' => [
            'header'            => '滿X件打折抵用券',
            'offer_value'       => '打折比例',
        ],
        'qty' => [
            'header'            => '滿X件Y件免費抵用券',
            'offer_value'       => '贈送數量',
        ],
    ],
    'item' => [
        'cond_value'       => '指定商品的條件數量',
        'cond_menu_item'   => '條件商品',
        'amount' => [
            'header'            => '指定商品現金抵用',
            'offer_value'       => '抵用金額',
            'offer_max_value'   => '數量上限'
        ],
        'discount' => [
            'header'            => '指定商品打折抵用',
            'offer_value'       => '打折比例',
            'offer_max_value'   => '數量上限'
        ],
        'qty' => [
            'header'            => '指定商品Y件抵用',
            'offer_value'       => '贈送數量',
            'offer_max_value'   => '數量上限'
        ],
        'item' => [
            'header'          => '指定商品免費(任選)',
            'offer_value'     => '贈送數量',
            'offer_max_value' => '數量上限',
            'offer_menu_item' => '贈送商品'
        ]
    ]
];


?>