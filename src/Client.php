<?php
/**
 * @desc Client
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 1:03
 */

declare(strict_types=1);

namespace Tinywan\WebmanRpc;


class Client
{
    /**
     * @param array $param
     * @return mixed
     */
    public function request(array $param)
    {
        $client = stream_socket_client(config('plugin.tinywan.webman-rpc.app.rpc.listen_tcp_address'));
        fwrite($client, json_encode($param)."\n"); // text协议末尾有个换行符"\n"
        $result = fgets($client, 10240000);
        return json_decode($result, true);
    }
}