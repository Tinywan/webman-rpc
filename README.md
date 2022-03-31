# simple rpc service for webman plugin

[![Latest Stable Version](http://poser.pugx.org/tinywan/rpc/v)](https://packagist.org/packages/tinywan/rpc)
[![Total Downloads](http://poser.pugx.org/tinywan/rpc/downloads)](https://packagist.org/packages/tinywan/rpc)
[![Latest Unstable Version](http://poser.pugx.org/tinywan/rpc/v/unstable)](https://packagist.org/packages/tinywan/rpc)
[![License](http://poser.pugx.org/tinywan/rpc/license)](https://packagist.org/packages/tinywan/rpc)

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

或者

```php
$client = stream_socket_client('tcp://127.0.0.1:9512');
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
