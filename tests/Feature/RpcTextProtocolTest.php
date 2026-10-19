<?php

declare(strict_types=1);

namespace service {
    class User
    {
        public function get(array $params = []): string
        {
            return (string) json_encode(['code' => 0, 'msg' => 'success', 'data' => $params]);
        }

        public function getArray(array $params = []): array
        {
            return ['code' => 0, 'msg' => 'success', 'data' => $params];
        }
    }
}

namespace {
    use Mockery as m;
    use Tinywan\Rpc\JsonParser;
    use Tinywan\Rpc\Protocol\RpcTextProtocol;
    use Workerman\Connection\TcpConnection;

    it('returns JSON_FORMAT_ERROR when payload is invalid json', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $connection->shouldReceive('send')
            ->once()
            ->with(m::on(function ($msg) {
                $decoded = json_decode($msg, true);
                return $decoded['code'] === JsonParser::JSON_FORMAT_ERROR;
            }))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, 'invalid-json-data');
        expect($result)->toBeTrue();
    });

    it('returns INVALID_REQUEST when class is missing or empty', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $payload = json_encode([
            'method' => 'get',
            'args' => [],
        ]);

        $connection->shouldReceive('send')
            ->once()
            ->with(m::on(function ($msg) {
                $decoded = json_decode($msg, true);
                return $decoded['code'] === JsonParser::INVALID_REQUEST;
            }))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, $payload);
        expect($result)->toBeTrue();
    });

    it('returns INVALID_REQUEST when method is missing or empty', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $payload = json_encode([
            'class' => 'User',
            'args' => [],
        ]);

        $connection->shouldReceive('send')
            ->once()
            ->with(m::on(function ($msg) {
                $decoded = json_decode($msg, true);
                return $decoded['code'] === JsonParser::INVALID_REQUEST;
            }))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, $payload);
        expect($result)->toBeTrue();
    });

    it('returns INVALID_CLASS_ERROR when requested class does not exist', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $payload = json_encode([
            'class' => 'NonExistentClass',
            'method' => 'someMethod',
            'args' => [],
        ]);

        $connection->shouldReceive('send')
            ->once()
            ->with(m::on(function ($msg) {
                $decoded = json_decode($msg, true);
                return $decoded['code'] === JsonParser::INVALID_CLASS_ERROR;
            }))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, $payload);
        expect($result)->toBeTrue();
    });

    it('returns INVALID_CLASS_ERROR when method does not exist or is not callable', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $payload = json_encode([
            'class' => 'User',
            'method' => 'nonExistentMethod',
            'args' => [],
        ]);

        $connection->shouldReceive('send')
            ->once()
            ->with(m::on(function ($msg) {
                $decoded = json_decode($msg, true);
                return $decoded['code'] === JsonParser::INVALID_CLASS_ERROR;
            }))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, $payload);
        expect($result)->toBeTrue();
    });

    it('successfully calls service method and sends result', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $payload = json_encode([
            'class' => 'User',
            'method' => 'get',
            'args' => [['id' => 101, 'name' => 'Tinywan']],
        ]);

        $connection->shouldReceive('send')
            ->once()
            ->with(json_encode([
                'code' => 0,
                'msg' => 'success',
                'data' => ['id' => 101, 'name' => 'Tinywan'],
            ]))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, $payload);
        expect($result)->toBeTrue();
    });

    it('automatically serializes array returned by service method to JSON string', function () {
        $protocol = new RpcTextProtocol();
        $connection = m::mock(TcpConnection::class);

        $payload = json_encode([
            'class' => 'User',
            'method' => 'getArray',
            'args' => [['id' => 102]],
        ]);

        $connection->shouldReceive('send')
            ->once()
            ->with(json_encode([
                'code' => 0,
                'msg' => 'success',
                'data' => ['id' => 102],
            ], JSON_UNESCAPED_UNICODE))
            ->andReturn(true);

        $result = $protocol->onMessage($connection, $payload);
        expect($result)->toBeTrue();
    });
}
