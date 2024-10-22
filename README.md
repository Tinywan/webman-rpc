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
