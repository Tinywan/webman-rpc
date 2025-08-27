# 单元测试

## 运行测试

### 安装依赖
```bash
composer install --dev
```

### 运行所有测试
```bash
./vendor/bin/phpunit
```

### 运行特定测试文件
```bash
./vendor/bin/phpunit tests/ConfigTest.php
```

### 运行特定测试方法
```bash
./vendor/bin/phpunit --filter testGetConfig tests/ConfigTest.php
```

### 生成测试覆盖率报告
```bash
./vendor/bin/phpunit --coverage-html coverage/
```

## 测试覆盖范围

- **ConfigTest**: 配置缓存类测试
- **ClientTest**: RPC客户端测试
- **RpcTextProtocolTest**: 文本协议处理器测试
- **ErrorTest**: 错误处理类测试
- **JsonParserTest**: JSON解析器测试
- **RpcResponseExceptionTest**: RPC响应异常测试
- **RpcUnexpectedValueExceptionTest**: RPC意外值异常测试

## 测试环境

- PHP >= 7.4
- PHPUnit ^9.0
- Mockery ^1.4

## 注意事项

1. 测试环境模拟了Webman框架的`config()`函数
2. 测试环境模拟了`response_rpc_json()`函数
3. 使用Mockery模拟TCP连接等外部依赖
4. 所有测试都独立运行，不依赖真实的RPC服务器