<?php

// 设置路径
set_include_path(__DIR__ . '/src' . PATH_SEPARATOR . __DIR__ . '/tests' . PATH_SEPARATOR . get_include_path());

// 模拟配置函数
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

// 模拟response_rpc_json函数
function response_rpc_json(int $code = 0, string $msg = 'success', array $data = []) {
    return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data], JSON_UNESCAPED_UNICODE);
}

// 自动加载类
spl_autoload_register(function ($class) {
    $prefix = 'Tinywan\\Rpc\\';
    $base_dir = __DIR__ . '/src/';
    
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

echo "=== RPC 单元测试 ===\n\n";

// 测试统计
$tests = [];
$passed = 0;
$failed = 0;

// 测试函数
function run_test($name, $callback) {
    global $tests, $passed, $failed;
    
    $tests[] = $name;
    echo "测试 $name: ";
    
    try {
        $callback();
        echo "✓ 通过\n";
        $passed++;
    } catch (Exception $e) {
        echo "✗ 失败 - " . $e->getMessage() . "\n";
        $failed++;
    }
}

// 开始测试

// 1. 测试Config类
run_test('Config::get()', function() {
    $config = \Tinywan\Rpc\Config::get();
    if (!isset($config['enable'])) {
        throw new Exception('配置获取失败');
    }
});

run_test('Config::getNamespace()', function() {
    $namespace = \Tinywan\Rpc\Config::getNamespace();
    if ($namespace !== 'service\\') {
        throw new Exception('命名空间获取失败');
    }
});

run_test('Config::getConnectTimeout()', function() {
    $timeout = \Tinywan\Rpc\Config::getConnectTimeout();
    if ($timeout !== 5) {
        throw new Exception('连接超时获取失败');
    }
});

// 2. 测试Error类
run_test('Error::make()', function() {
    $error = \Tinywan\Rpc\Error::make(400, 'Bad Request', ['field' => 'value']);
    if ($error->getCode() !== 400) {
        throw new Exception('错误码设置失败');
    }
});

run_test('Error::jsonSerialize()', function() {
    $error = \Tinywan\Rpc\Error::make(500, 'Server Error');
    $serialized = $error->jsonSerialize();
    if (!isset($serialized['code']) || !isset($serialized['message'])) {
        throw new Exception('JSON序列化失败');
    }
});

// 3. 测试JsonParser类
run_test('JsonParser::VERSION', function() {
    $version = \Tinywan\Rpc\JsonParser::VERSION;
    if ($version !== '1.0') {
        throw new Exception('版本常量错误');
    }
});

run_test('JsonParser constants', function() {
    $constants = [
        'PARSER_ERROR' => 32700,
        'INVALID_REQUEST' => 32600,
        'METHOD_NOT_FOUND' => 32601,
        'INVALID_PARAMS' => 32602,
        'INTERNAL_ERROR' => 32603,
    ];
    
    foreach ($constants as $name => $value) {
        if (constant('Tinywan\Rpc\JsonParser::' . $name) !== $value) {
            throw new Exception("常量 $name 值错误");
        }
    }
});

// 4. 测试Client类
run_test('Client construction', function() {
    $client = new \Tinywan\Rpc\Client('tcp://127.0.0.1:9512');
    if (!($client instanceof \Tinywan\Rpc\Client)) {
        throw new Exception('客户端创建失败');
    }
});

// 5. 测试异常类
run_test('RpcResponseException', function() {
    $error = \Tinywan\Rpc\Error::make(500, 'Server Error');
    $exception = new \Tinywan\Rpc\Exception\RpcResponseException($error);
    if ($exception->getCode() !== 500) {
        throw new Exception('异常代码设置失败');
    }
});

run_test('RpcUnexpectedValueException', function() {
    $exception = new \Tinywan\Rpc\Exception\RpcUnexpectedValueException('Test message');
    if ($exception->getMessage() !== 'Test message') {
        throw new Exception('异常消息设置失败');
    }
});

// 6. 测试辅助函数
run_test('response_rpc_json()', function() {
    $result = response_rpc_json(0, 'success', ['test' => 'data']);
    $decoded = json_decode($result, true);
    if (!isset($decoded['code']) || $decoded['code'] !== 0) {
        throw new Exception('响应函数失败');
    }
});

// 输出结果
echo "\n=== 测试结果 ===\n";
echo "总计: " . count($tests) . "\n";
echo "通过: $passed\n";
echo "失败: $failed\n";
echo "成功率: " . round(($passed / count($tests)) * 100, 2) . "%\n";

if ($failed > 0) {
    echo "\n❌ 有 $failed 个测试失败\n";
    exit(1);
} else {
    echo "\n✅ 所有测试通过！\n";
    exit(0);
}

?>