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
            fwrite($resource, json_encode($param)."\n");
            $result = fgets($resource, 10240000);
            fclose($resource);
            return json_decode($result, true);
        }catch (\Throwable $throwable) {
            throw new RpcUnexpectedValueException('rpc request failed: '.$throwable->getMessage());
        }
    }
}
