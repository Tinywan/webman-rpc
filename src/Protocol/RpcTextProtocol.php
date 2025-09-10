<?php

/**
 * @desc RpcTextProtocol
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 0:36
 */

declare(strict_types=1);

namespace Tinywan\Rpc\Protocol;

use Throwable;
use Tinywan\ExceptionHandler\Logger;
use Tinywan\Rpc\JsonParser;
use Workerman\Connection\TcpConnection;

class RpcTextProtocol
{
    /**
     * @param TcpConnection $connection
     * @param string $string
     * @return bool|null
     */
    public function onMessage(TcpConnection $connection, string $string): ?bool
    {
        try {
            static $instances = [];

            $data = json_decode($string, true);
            $error = json_last_error();
            if ($error != JSON_ERROR_NONE) {
                return JsonParser::encode($connection,
                    JsonParser::JSON_FORMAT_ERROR,
                    sprintf('Data(%s) is not json format!', $string));
            }

            $config = config('plugin.tinywan.rpc.app');
            $class = $config['server']['namespace'] . $data['class'];
            if (!class_exists($class)) {
                return JsonParser::encode($connection,
                    JsonParser::INVALID_CLASS_ERROR,
                    sprintf('%s Class is not exist!', $data['class']));
            }

            $method = $data['method'];
            if (!method_exists($class, (string)$method)) {
                return JsonParser::encode($connection,
                    JsonParser::INVALID_CLASS_ERROR,
                    sprintf('%s Method is not exist!', $method));
            }
            $args = $data['args'] ?? [];
            if (!isset($instances[$class])) {
                $instances[$class] = new $class();
            }
            return $connection->send(call_user_func_array([$instances[$class], $method], $args));
        } catch (Throwable $th) {
            Logger::error('RPC Service Exception ' . $th->getMessage(), [
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine()
            ]);
            return JsonParser::encode($connection, JsonParser::SERVER_ERROR, $th->getMessage());
        }
    }
}
