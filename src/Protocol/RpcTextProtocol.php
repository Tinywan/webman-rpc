<?php
/**
 * @desc RpcTextProtocol
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 0:36
 */

declare(strict_types=1);

namespace Tinywan\WebmanRpc\Protocol;


use Workerman\Connection\TcpConnection;

class RpcTextProtocol
{
    /**
     * @param TcpConnection $connection
     * @param $data
     */
    public function onMessage(TcpConnection $connection, $data)
    {
        static $instances = [];
        $data = json_decode($data, true);
        $class = 'service\\'.$data['class'];
        $method = $data['method'];
        $args = $data['args'];
        if (!isset($instances[$class])) {
            $instances[$class] = new $class;
        }
        $connection->send(call_user_func_array([$instances[$class], $method], $args));
    }
}