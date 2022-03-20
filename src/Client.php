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
     * @param array $param
     * @return mixed
     */
    public static function request(array $param)
    {
        $client = stream_socket_client(config('plugin.tinywan.webman-rpc.app.rpc.listen_tcp_address'), $errno, $errorMessage);
        if (false === $client) {
            throw new RpcUnexpectedValueException('Failed to connect: '.$errorMessage);
        }
        // fwrite() - 写入文件（可安全用于二进制文件）
        fwrite($client, json_encode($param)."\n"); // text协议末尾有个换行符"\n"
        // fgets 从文件指针中读取一行
        $result = fgets($client, 10240000);
        return json_decode($result, true);
    }
}