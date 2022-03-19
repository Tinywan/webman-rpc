<?php
/**
 * @desc RpcClient
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 1:03
 */

declare(strict_types=1);

namespace Tinywan\WebmanRpc;


class RpcClient
{
    public function test()
    {
        $client = stream_socket_client(config('plugin.tinywan.webman-rpc.app.rpc.listen_tcp_address'));
        $request = [
            'class'   => 'User',
            'method'  => 'get',
            'args'    => [100999], // 100 是 $uid
        ];
        fwrite($client, json_encode($request)."\n"); // text协议末尾有个换行符"\n"
        $result = fgets($client, 10240000);
        $result = json_decode($result, true);
        return $result;
    }
}