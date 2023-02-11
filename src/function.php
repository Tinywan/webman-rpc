<?php

// 响应RPC数据
if (!function_exists('response_rpc_json')) {
    /**
     * @desc: 响应RPC数据
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return false|string
     * @author Tinywan(ShaoBo Wan)
     */
    function response_rpc_json(int $code = 0, string $msg = 'success', array $data = [])
    {
        return json_encode(['code' => $code, 'msg' => $msg, 'data' => $data],JSON_UNESCAPED_UNICODE);
    }
}