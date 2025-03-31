<?php

return [
    'enable' => true,
    'server' => [
        'namespace'=> 'service\\', // 自定义服务命名空间
        'listen_text_address' => 'text://0.0.0.0:9512', // 自定义Text协议地址
    ],
    'connect_timeout' => 5, // 超时时间
    'request_timeout' => 5, // 请求超时时间
];
