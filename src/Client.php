<?php
/**
 * @desc Client
 *
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 1:03
 */
declare(strict_types=1);

namespace Tinywan\Rpc;

use Tinywan\Rpc\Exception\RpcResponseException;
use Tinywan\Rpc\Exception\RpcUnexpectedValueException;

class Client
{
    /**
     * @var string
     */
    private $address;

    /**
     * Client constructor.
     */
    public function __construct(string $address)
    {
        $this->address = $address;
    }

    /**
     * @return mixed
     */
    public function request(array $param)
    {
        $resource = null;
        try {
            // 连接阶段超时
            $connectTimeout = config('plugin.tinywan.rpc.app.connect_timeout') ?? 3;
            $resource = stream_socket_client(
                $this->address,
                $errno,
                $errorMessage,
                $connectTimeout
            );

            if (!is_resource($resource)) {
                throw new RpcUnexpectedValueException('rpc request failed: ' . $errorMessage);
            }

            // 读写超时
            $timeout = (int)($param['timeout'] ?? config('plugin.tinywan.rpc.app.request_timeout') ?? 5);
            stream_set_timeout($resource, $timeout);

            // 发送请求
            fwrite($resource, json_encode($param)."\n");

            // 实时检测超时
            $info = stream_get_meta_data($resource);
            if ($info['timed_out']) {
                throw new RpcResponseException(Error::make(408, 'rpc request timeout'));
            }

            $result = fgets($resource, 10240000);
            if ($result){
                return json_decode(trim($result), true);
            }
        } catch (\Throwable $e) {
            throw new RpcUnexpectedValueException('rpc request failed: '.$e->getMessage());
        } finally {
            if ($resource && is_resource($resource)) {
                fclose($resource);
            }
        }
    }
}
