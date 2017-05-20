<?php

return [
    \Modules\Auth\Models\Member::class => [
        'name' => '用户',
        'enum' => [
            'member_status' => [
                'normal' => '正常',
                'forbid' => '禁止',
            ]
        ]
    ],
    \Modules\Auth\Models\Member::class => [
        'name' => '卡片',
        'enum' => [
            'card_status' => [
                'flow' => '流转中',
                'done' => '流转完成',
                'error' => '流转异常',
            ]
        ]
    ]
];