<?php

declare(strict_types=1);

use Mockery as m;
use Tinywan\Rpc\JsonParser;
use Workerman\Connection\TcpConnection;

it('has correct jsonrpc constants defined', function () {
    expect(JsonParser::VERSION)->toBe('1.0')
        ->and(JsonParser::OK)->toBe(0)
        ->and(JsonParser::JSON_FORMAT_ERROR)->toBe(400)
        ->and(JsonParser::INVALID_CLASS_ERROR)->toBe(404)
        ->and(JsonParser::SERVER_ERROR)->toBe(500)
        ->and(JsonParser::PARSER_ERROR)->toBe(32700)
        ->and(JsonParser::INVALID_REQUEST)->toBe(32600)
        ->and(JsonParser::METHOD_NOT_FOUND)->toBe(32601)
        ->and(JsonParser::INVALID_PARAMS)->toBe(32602)
        ->and(JsonParser::INTERNAL_ERROR)->toBe(32603);
});

it('encodes data and sends through connection correctly', function () {
    $connection = m::mock(TcpConnection::class);
    $connection->shouldReceive('send')
        ->once()
        ->with(json_encode([
            'code' => 0,
            'msg' => 'success',
            'data' => ['id' => 123, 'name' => 'Tinywan'],
        ], JSON_UNESCAPED_UNICODE))
        ->andReturn(true);

    $result = JsonParser::encode($connection, 0, 'success', ['id' => 123, 'name' => 'Tinywan']);
    expect($result)->toBeTrue();
});

it('can encode with default empty data', function () {
    $connection = m::mock(TcpConnection::class);
    $connection->shouldReceive('send')
        ->once()
        ->with(json_encode([
            'code' => 0,
            'msg' => 'ok',
            'data' => [],
        ], JSON_UNESCAPED_UNICODE))
        ->andReturn(true);

    $result = JsonParser::encode($connection, 0, 'ok');
    expect($result)->toBeTrue();
});

it('can encode with null data', function () {
    $connection = m::mock(TcpConnection::class);
    $connection->shouldReceive('send')
        ->once()
        ->with(json_encode([
            'code' => 200,
            'msg' => 'Success',
            'data' => null,
        ], JSON_UNESCAPED_UNICODE))
        ->andReturn(true);

    $result = JsonParser::encode($connection, 200, 'Success', null);
    expect($result)->toBeTrue();
});

it('preserves unicode characters when encoding', function () {
    $connection = m::mock(TcpConnection::class);
    $expectedJson = json_encode([
        'code' => 0,
        'msg' => '操作成功',
        'data' => ['title' => '中文测试'],
    ], JSON_UNESCAPED_UNICODE);

    $connection->shouldReceive('send')
        ->once()
        ->with($expectedJson)
        ->andReturn(true);

    $result = JsonParser::encode($connection, 0, '操作成功', ['title' => '中文测试']);
    expect($result)->toBeTrue();
});
