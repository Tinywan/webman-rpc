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
            if ($error != JSON_ERROR_NONE || !is_array($data)) {
                return JsonParser::encode($connection,
                    JsonParser::JSON_FORMAT_ERROR,
                    sprintf('Data(%s) is not json format!', $string));
            }

            if (!isset($data['class']) || !is_string($data['class']) || empty($data['class'])) {
                return JsonParser::encode($connection,
                    JsonParser::INVALID_REQUEST,
                    'Invalid Request: class is required');
            }

            if (!isset($data['method']) || !is_string($data['method']) || empty($data['method'])) {
                return JsonParser::encode($connection,
                    JsonParser::INVALID_REQUEST,
                    'Invalid Request: method is required');
            }

            $namespace = \Tinywan\Rpc\Config::getNamespace();
            $class = $namespace . ltrim($data['class'], '\\');
            if (!class_exists($class)) {
                return JsonParser::encode($connection,
                    JsonParser::INVALID_CLASS_ERROR,
                    sprintf('%s Class is not exist!', $data['class']));
            }

            if (!isset($instances[$class])) {
                $instances[$class] = new $class();
            }

            $method = $data['method'];
            if (!is_callable([$instances[$class], $method])) {
                return JsonParser::encode($connection,
                    JsonParser::INVALID_CLASS_ERROR,
                    sprintf('%s Method is not exist!', $method));
            }

            $args = isset($data['args']) && is_array($data['args']) ? $data['args'] : [];
            $response = call_user_func_array([$instances[$class], $method], $args);
            if (!is_string($response)) {
                $response = (string) json_encode($response, JSON_UNESCAPED_UNICODE);
            }

            return $connection->send($response);
        } catch (Throwable $th) {
            $errorMsg = 'RPC Service Exception '.$th->getMessage();
            if (class_exists('\think\db\exception\DbException') && $th instanceof \think\db\exception\DbException) {
                $dbData = $th->getData()['Database Status'] ?? [];
                $errorMsg .= " \n" . '   > - Code：```' . $dbData['Error Code'] . "``` \n";
                $errorMsg .= " \n" . '   > - Message：```' . $dbData['Error Message'] . "``` \n";
                $errorMsg .= " \n" . '   > - SQL：```' . $dbData['Error SQL'] . "``` \n";
            }
            Logger::error($errorMsg, [
                'error' => $th->getMessage(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'domain' => $class ?? '',
                'request_url' => $method ?? '',
                'request_param' => $args ?? [],
            ]);
            return JsonParser::encode($connection, JsonParser::SERVER_ERROR, $th->getMessage());
        }
    }
}
