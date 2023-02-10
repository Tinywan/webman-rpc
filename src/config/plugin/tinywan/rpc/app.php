<?php

return [
    'enable' => true,
    'service' => [
        'namespace'=> 'service\\', // 自定义服务命名空间
        'listen_text_address' => 'text://0.0.0.0:9512', // 自定义Text协议地址
    ],
    // 异常响应码和消息定义
    'response' => [
        'class'=> [
            'code' => 404,
            'msg' => '服务类不存在',
        ],
        'method'=> [
            'code' => 404,
            'msg' => '服务类方法不存在',
        ],
    ]
];
