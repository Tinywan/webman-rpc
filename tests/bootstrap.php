<?php

// 设置错误报告
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 设置时区
date_default_timezone_set('UTC');

// 模拟Webman框架的config函数
if (!function_exists('config')) {
    function config($key) {
        $config = [
            'plugin.tinywan.rpc.app' => [
                'enable' => true,
                'server' => [
                    'namespace' => 'service\\',
                    'listen_text_address' => 'text://0.0.0.0:9512',
                ],
                'connect_timeout' => 5,
                'request_timeout' => 5,
            ]
        ];
        
        return $config[$key] ?? null;
    }
}

// 模拟response_rpc_json函数
if (!function_exists('response_rpc_json')) {
    function response_rpc_json(int $code = 0, string $msg = 'success', array $data = []) {
        return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
    }
}

// 自动加载测试类
spl_autoload_register(function ($class) {
    $prefix = 'Tinywan\\Rpc\\Tests\\';
    $base_dir = __DIR__ . '/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});