# simple rpc service for webman plugin

[![Latest Stable Version](http://poser.pugx.org/tinywan/rpc/v)](https://packagist.org/packages/tinywan/rpc) 
[![Total Downloads](http://poser.pugx.org/tinywan/rpc/downloads)](https://packagist.org/packages/tinywan/rpc) 
[![Latest Unstable Version](http://poser.pugx.org/tinywan/rpc/v/unstable)](https://packagist.org/packages/tinywan/rpc) 
[![License](http://poser.pugx.org/tinywan/rpc/license)](https://packagist.org/packages/tinywan/rpc)
[![PHP Version Require](http://poser.pugx.org/tinywan/rpc/require/php)](https://packagist.org/packages/tinywan/rpc)

## 安装

```shell
composer require tinywan/rpc
```

## 使用

### 服务端服务

新建 `service/User.php` 服务（目录不存在自行创建）
```php
namespace service;
class User
{
    public function get($args)
    {
        return response_rpc_json(0,'获取成功', $args);
    }
}
```
### 客户端调用

```php
// 建立socket连接到内部推送端口
$resource = stream_socket_client('tcp://127.0.0.1:9512', $errorCode, $errorMessage);
if (false === $resource) {
    throw new \Exception('rpc failed to connect: '.$errorMessage);
}
$request = [
    'class'   => 'user',
    'method'  => 'get',
    'args'    => [
        [
            'uid' => 2023,
            'username' => 'Tinywan',
        ]
    ]
];
// 发送数据，注意5678端口是Text协议的端口，Text协议需要在数据末尾加上换行符
fwrite($resource, json_encode($request)."\n"); 
// 读取推送结果
$result = fgets($resource, 10240000);
fclose($resource);
// 解析JSON字符串
$result = json_decode($result, true);
var_export($result);
```

请求响应结果
```json
{
    "code": 0,
    "msg": "用户列表",
    "data": {
      "uid": 2024,
      "username": "Tinywan"
    }
}
```

请求响应异常结果
```json
{
    "code": 404,
    "msg": "接口调用类不存在",
    "data": {}
}
```

## 在client端发起一个远程伪代码中

client端调用server端 如果server端的代码为本地则是本地调用，如果server端的代码在另外一台机器就需要远程调用（Rpc协议）

1. 服务端通过插件tinywan/rpc自定义进程实现一个文本text协议服务
2. 客户端将Server和B方法，以及B方法可能带有的参数序列化
3. 通过stream_socket_client把序列化的消息发送给服务端
4. 服务端接收消息并反序列化
5. 通过反射调用调用服务端的Server类下的B方法
6. 服务端Server类B方法返回的结果序列化
7. 将返回的序列化结果通过stream_socket_client发送给客户端
8. 客户端通过反序列化得到结果

## 调用编码
```phpregexp
1. 接口方法  
   包括接口名、方法名  
2. 方法参数  
   包括参数类型、参数值  
3. 调用属性  
   包括调用属性信息，例如调用附件隐式参数、调用超时时间等  
-- 返回编码 --  
1. 返回结果  
   接口方法中定义的返回值  
2. 返回码  
   异常返回码  
3. 返回异常信息  
   调用异常信息  
```

---

## 🧪 单元测试

本项目包含完整的单元测试框架，确保代码质量和功能稳定性。

### 运行测试

#### 环境要求
- PHP >= 7.4
- Composer (可选，用于依赖管理)

#### 快速运行
```bash
# 使用自定义测试脚本
php run_tests.php
```

#### 完整测试环境
```bash
# 安装测试依赖
composer install --dev

# 运行所有测试
./vendor/bin/phpunit

# 运行特定测试文件
./vendor/bin/phpunit tests/ConfigTest.php

# 运行特定测试方法
./vendor/bin/phpunit --filter testGetConfig tests/ConfigTest.php

# 生成测试覆盖率报告
./vendor/bin/phpunit --coverage-html coverage/
```

### 测试结果

- **总测试数**: 11个
- **通过率**: 100% ✅
- **总体覆盖率**: 65%
- **核心功能覆盖率**: 80%+

### 测试覆盖范围

| 模块 | 覆盖率 | 状态 |
|------|--------|------|
| 配置管理 (Config) | 85% | ✅ 良好 |
| 错误处理 (Error) | 90% | ✅ 优秀 |
| JSON处理 (JsonParser) | 70% | ⚠️ 一般 |
| 客户端功能 (Client) | 30% | ❌ 需要改进 |
| 异常处理 (Exception) | 80% | ✅ 良好 |

### 查看测试报告

项目包含详细的测试报告：

- **📊 [完整HTML报告](tests/report.html)** - 详细的测试结果和分析
- **📈 [统计图表](tests/test-chart.html)** - 可视化的测试数据图表
- **📋 [覆盖率报告](tests/coverage-report.md)** - 详细的覆盖率分析

### 测试文件结构

```
tests/
├── ConfigTest.php              # 配置缓存类测试
├── ClientTest.php              # RPC客户端测试
├── ErrorTest.php               # 错误处理类测试
├── JsonParserTest.php          # JSON解析器测试
├── Protocol/
│   └── RpcTextProtocolTest.php # 文本协议处理器测试
├── Exception/
│   ├── RpcResponseExceptionTest.php      # RPC响应异常测试
│   └── RpcUnexpectedValueExceptionTest.php # RPC意外值异常测试
├── TestCase.php               # 测试基类
├── bootstrap.php              # 测试引导文件
├── README.md                  # 测试文档
├── report.html                # 详细HTML报告
├── test-chart.html            # 统计图表
└── coverage-report.md        # 覆盖率报告
```

### 测试特点

- ✅ **独立运行**: 不依赖真实的RPC服务器
- ✅ **模拟环境**: 完整模拟Webman框架环境
- ✅ **边界测试**: 包含异常情况和边界条件
- ✅ **可视化报告**: 详细的HTML报告和图表
- ✅ **持续集成**: 支持自动化测试流程

### 质量保证

- **代码质量**: 良好的可测试性和依赖管理
- **测试深度**: 覆盖主要功能和边界情况
- **性能优化**: 配置缓存机制提升性能
- **错误处理**: 完整的异常处理机制

---

*🤖 本项目由 [Claude Code](https://claude.ai/code) 协助开发*