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

            $request = json_decode($string, true);
            if (!$request) {
                // JSON RPC 2.0标准错误码-32700
                $response = [
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => -32700,
                        'message' => 'Parse error',
                        'data' => 'Invalid JSON'
                    ],
                    'id' => null
                ];
                return $connection->send(json_encode($response) . "\n");
            }

            // 验证JSON RPC 2.0格式
            if ($request['jsonrpc'] !== '2.0') {
                $response = [
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => -32600,
                        'message' => 'Invalid Request',
                        'data' => 'jsonrpc must be "2.0"'
                    ],
                    'id' => $request['id'] ?? null
                ];
                return $connection->send(json_encode($response) . "\n");
            }

            $classMethod = explode('.', $request['method']);
            if (count($classMethod) !== 2) {
                $response = [
                    'jsonrpc' => '2.0',
                    'error' => [
                        'code' => -32601,
                        'message' => 'Method not found',
                        'data' => 'Invalid method format'
                    ],
                    'id' => $request['id'] ?? null
                ];
                return $connection->send(json_encode($response) . "\n");
            }
            [$className, $methodName] = $classMethod;
            $config = config('plugin.tinywan.rpc.app');
            $class = $config['server']['namespace'] . $className;
            if (!class_exists($class)) {
                throw new \Exception(sprintf('%s Class is not exist!', $className), 405);
            }

            if (!method_exists($class, (string)$methodName)) {
                throw new \Exception(sprintf('%s method is not exist!', $methodName), 405);
            }
            $args = $data['args'] ?? [];
            if (!isset($instances[$class])) {
                $instances[$class] = new $class();
            }
            return $connection->send(call_user_func_array([$instances[$class], $methodName], $args));
        } catch (Throwable $th) {
            $errorCode = -32000; // 自定义错误基础码
            if ($th->getCode() === 404 || $th->getCode() === 405) {
                $errorCode = -32601;
            }

            $response = [
                'jsonrpc' => '2.0',
                'error' => [
                    'code' => $errorCode,
                    'message' => $th->getMessage(),
                    'data' => ['file' => $th->getFile(), 'line' => $th->getLine()]
                ],
                'id' => $request['id'] ?? null
            ];
            return $connection->send(json_encode($response) . "\n");
        }
    }
}
