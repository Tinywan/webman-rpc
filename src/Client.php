<?php
/**
 * @desc Client
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 1:03
 */
declare(strict_types=1);

namespace Tinywan\WebmanRpc;


use Tinywan\WebmanRpc\Exception\RpcUnexpectedValueException;

class Client
{
    /**
     * @var string
     */
    private string $address;

    /**
     * Client constructor.
     * @param string $address
     */
    public function __construct(string $address)
    {
        $this->address = $address;
    }

    /**
     * @param array $param
     * @return mixed
     */
    public function request(array $param)
    {
        $client = stream_socket_client($this->address, $errno, $errorMessage);
        if (false === $client) {
            throw new RpcUnexpectedValueException('rpc failed to connect: '.$errorMessage);
        }
        fwrite($client, json_encode($param)."\n");
        $result = fgets($client, 10240000);
        return json_decode($result, true);
    }
}