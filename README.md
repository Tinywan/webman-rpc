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

新建 `service/User.php` 服务(目录不存在自行创建)

```php
namespace service;
class User
{
    public function get($uid)
    {
        return json_encode([
            'uid'  => $uid,
            'name' => 'webman'
        ]);
    }
}
```
### 客户端调用

webman框架使用
```php
use Tinywan\Rpc\Client;
$request = [
    'class'   => 'User',
    'method'  => 'get',
    'args'    => [2022]
];
$client = new Client('tcp://127.0.0.1:9512');
$res = $client->request($request);
var_export($res);
```

非webman框架使用

```php
$client = stream_socket_client('tcp://127.0.0.1:9512', $errorCode, $errorMessage);
if (false === $client) {
    throw new \Exception('rpc failed to connect: '.$errorMessage);
}
$request = [
    'class'   => 'User',
    'method'  => 'get',
    'args'    => [2022]
];
fwrite($client, json_encode($request)."\n"); // text协议末尾有个换行符"\n"
$result = fgets($client, 10240000);
$result = json_decode($result, true);
var_export($result);
```

最终结果打印

```phpregexp
array (
  'uid' => 2022,
  'name' => 'webman',
)
```

## Other
```php
// Call the $foo->bar() method with 2 arguments
$foo = new foo;
call_user_func_array([$foo, "bar"], ["three", "four"]);
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
