<?php

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
        'title'             => '名稱',
        'description'       => '說明',
        'start_at'          => '起始時間',
        'end_at'            => '結束時間',
        'duration'          => '期間',
        'remark'            => '備註',
        'max_qty'           => '序號數',
        'user_use_count'    => '使用次數',
        'max_value'         => '活動最高折抵金額',
        'status'            => '狀態',
        'plural'            => '優惠是否合併使用',
        'repeat'            => '優惠是否可以累計',
        'product_delivery'  => '取餐方式',
        'hidden'            => 'App隱藏',
        'sort_by'           => '優先權',
        'default'           => '預設',
    ],
    'amount' => [
        'cond_value' => '滿額金額',
        'menu_item'  => '挑選條件商品',
        'amount' => [
            'header'      => '合計金額折扣多少錢',
            'offer_value' => '折扣多少錢'
        ],
        'discount' => [
            'header'            => '合計金額打折',
            'offer_value'       => '打多少折(%)',
        ],
        'qty' => [
            'header'            => '滿額Y件免費抵用',
            'offer_value'       => '贈送數量',
            'offer_max_value'   => '最高贈送數量'
        ]
    ],
    'qty'    => [
        'cond_value' => '滿件數量',
        'menu_item'  => '挑選條件商品',   
        'amount' => [
            'header'      => '合計金額折扣多少錢',
            'offer_value' => '折扣多少錢'
        ],
        'amount_item_group_n' => [
            'header'          => '那些商品第幾件金額折扣多少錢',
            'offer_value'     => '最低價錢的第幾件金額折扣多少錢',
            'price'           => '變多少錢',
            'nth'             => '最低價的第幾件',
            'offer_max_value' => '最多幾件商品變更價錢',
        ],
        'discount' => [
            'header'      => '滿X件打折抵用',
            'offer_value' => '打多少折(%)',
        ],
        'discount_item_group_n' => [
            'header'          => '那些商品第幾件金額打折',
            'offer_value'     => '打多少折(%)',
            'price'           => '變多少錢',
            'nth'             => '最低價的第幾件',
            'offer_max_value' => '最多幾件商品變更價錢',
        ],
        'discount_item_group' => [
            'header'          => '合計金額打折',
            'offer_value'     => '打多少折(%)',
            'price'           => '變多少錢',
            'nth'             => '最低價的第幾件',
            'offer_max_value' => '最多幾件商品變更價錢',
        ],
        'qty' => [
            'header'          => '最低價的幾件商品變多少錢',
            'offer_value'     => '最低價的幾件商品',
            'price'           => '變多少錢',
            'offer_max_value' => '最多幾件商品變更價錢',
        ],
        'qty_item_group_n' => [
            'header'          => '那些商品最低價第幾件變多少錢',
            'offer_value'     => '最低價的幾件商品',
            'price'           => '變多少錢',
            'nth'             => '最低價的第幾件',
            'offer_max_value' => '最多幾件商品變更價錢',
        ]
    ],
    'registed' => [
        'cond_value' => '註冊',
        'registed' => [
            'header'       => '註冊禮',
            'offer_value'  => '優惠券',
            'offer_points' => '贈送點數'
        ],
    ],
];
?>