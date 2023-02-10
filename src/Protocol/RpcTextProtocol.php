<?php
/**
 * @desc RpcTextProtocol
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 0:36
 */
declare(strict_types=1);

namespace Tinywan\Rpc\Protocol;

use Workerman\Connection\TcpConnection;

class RpcTextProtocol
{
    /**
     * @param TcpConnection $connection
     * @param string $data
     * @return bool|null
     */
    public function onMessage(TcpConnection $connection, string $data): ?bool
    {
        static $instances = [];
        $data = json_decode($data, true);
        $config = config('plugin.tinywan.rpc.app');
        $class = $config['rpc']['namespace'].$data['class'];
        if (!class_exists($class)) {
            return $connection->send(json_encode([
                'code' => $config['response']['class']['code'],
                'msg' => $class. $config['response']['class']['msg']
            ]));
        }

        $method = $data['method'];
        if (!method_exists($class,(string) $method)) {
            return $connection->send(json_encode([
                'code' => $config['response']['method']['code'],
                'msg' => $method. $config['response']['method']['msg']
            ]));
        }
        $args = $data['args'];
        if (!isset($instances[$class])) {
            $instances[$class] = new $class();
        }
        return $connection->send(call_user_func_array([$instances[$class], $method], $args));
    }
}
