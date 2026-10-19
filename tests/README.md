# Tests

本项目使用 [Pest PHP](https://pestphp.com/) 进行单元与功能测试。

## 目录结构

```text
tests/
├── Pest.php                     # Pest 全局配置与环境绑定
├── TestCase.php                 # 测试基类（提供 Webman 配置模拟与环境清理）
├── Unit/                        # 单元测试
│   ├── ConfigTest.php           # Tinywan\Rpc\Config 缓存与配置获取
│   ├── ErrorTest.php            # Tinywan\Rpc\Error 对象与序列化
│   ├── ExceptionTest.php        # RPC 响应与意外值异常
│   ├── FunctionTest.php         # response_rpc_json() 辅助函数
│   └── JsonParserTest.php       # JSON-RPC 常量定义与打包响应
└── Feature/                     # 功能 / 协议测试
    ├── ClientTest.php           # RPC 客户端连接与请求行为
    └── RpcTextProtocolTest.php  # Text 协议分发、服务调用与异常拦截
```

## 测试执行

在本地 Docker 容器 `dnmp-php74` 中执行：

```bash
docker exec -w /var/www/webman/webman-rpc dnmp-php74 composer test
```
