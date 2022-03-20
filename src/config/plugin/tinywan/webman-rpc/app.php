<?php
return [
    'enable' => true,
    'rpc' => [
        'namespace'=> 'service\\', // 自定义Text协议地址
        'listen_text_address' => 'text://0.0.0.0:9512', // 自定义Text协议地址
        'listen_tcp_address' => 'tcp://127.0.0.1:9512', // tcp
    ]
];