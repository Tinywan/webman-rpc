<?php
/**
 * @desc JsonParser
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2023/2/10 23:28
 */

declare(strict_types=1);

namespace Tinywan\Rpc;


use Workerman\Connection\TcpConnection;

class JsonParser
{
    /**
     * Json-rpc version
     */
    const VERSION = '1.0';

    /**
     * Delimiter
     */
    const DELIMITER = "@";

    /**
     * ok code
     */
    const OK = 0;

    /**
     * JSON-RPC error code
     */
    const JSON_FORMAT_ERROR = 400;

    /**
     * Invalid class error
     */
    const INVALID_CLASS_ERROR = 404;

    /**
     * Server error
     */
    const SERVER_ERROR = 500;

    /**
     * Parser error
     */
    const PARSER_ERROR = 32700;

    /**
     * Invalid Request
     */
    const INVALID_REQUEST = 32600;

    /**
     * Method not found
     */
    const METHOD_NOT_FOUND = 32601;

    /**
     * Invalid params
     */
    const INVALID_PARAMS = 32602;

    /**
     * Internal error
     */
    const INTERNAL_ERROR = 32603;

    /**
     * @desc 编码JSON
     * @param TcpConnection $connection
     * @param int $code
     * @param string $msg
     * @param array $data
     * @return bool|null
     */
    public static function encode(TcpConnection $connection, int $code, string $msg, array $data = []): ?bool
    {
        return $connection->send(json_encode([
            'code' => $code,
            'msg' => $msg,
            'data' => $data,
        ], JSON_UNESCAPED_UNICODE));
    }
}