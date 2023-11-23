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
        try {
            $resource = stream_socket_client($this->address, $errno, $errorMessage);
            if (false === $resource) {
                throw new RpcUnexpectedValueException('rpc failed to connect: '.$errorMessage);
            }

            // 如果param数组里面存在timeout参数，就设置超时时间
            $timeout = $param['timeout'] ?? 0;
            if ($timeout > 0){
                stream_set_timeout($resource, $timeout);
            }

            fwrite($resource, json_encode($param)."\n");
            $result = fgets($resource, 10240000);

            // 检查是否超时,并报超时异常
            $info = stream_get_meta_data($resource);
            if ($info['timed_out']) {
                throw new RpcResponseException(Error::make(408, 'rpc request timeout'));
            }

            fclose($resource);
            return json_decode($result, true);
        }catch (\Throwable $throwable) {
            throw new RpcUnexpectedValueException('rpc request failed: '.$throwable->getMessage());
        }
    }
}
