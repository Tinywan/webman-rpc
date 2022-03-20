# webman-rpc


## 服务端服务

新建 `service/User.php` 服务(目录不存在自行创建)
```php
<?php
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
## 客户端调用
```php
use Tinywan\WebmanRpc\Client;
$request = [
    'class'   => 'User',
    'method'  => 'get',
    'args'    => [2022], // 100 是 $uid
];
$client = new Client();
$res = $client->request($request);
```
最终结果打印
```
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
> https://www.workerman.net/q/6057
