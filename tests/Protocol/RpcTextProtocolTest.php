<?php

namespace Tinywan\Rpc\Tests\Protocol;

use Tinywan\Rpc\Protocol\RpcTextProtocol;
use Tinywan\Rpc\JsonParser;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class RpcTextProtocolTest extends TestCase
{
    private $protocol;
    private $mockConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->protocol = new RpcTextProtocol();
        $this->mockConnection = m::mock('Workerman\Connection\TcpConnection');
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function testOnMessageWithValidJson()
    {
        $validJson = json_encode([
            'class' => 'user',
            'method' => 'get',
            'args' => [['id' => 1]]
        ]);

        // 模拟配置函数
        if (!function_exists('config')) {
            function config($key) {
                return [
                    'enable' => true,
                    'server' => [
                        'namespace' => 'service\\',
                        'listen_text_address' => 'text://0.0.0.0:9512',
                    ],
                    'connect_timeout' => 5,
                    'request_timeout' => 5,
                ][$key] ?? null;
            }
        }

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $validJson);
        $this->assertTrue($result);
    }

    public function testOnMessageWithInvalidJson()
    {
        $invalidJson = 'invalid json string';

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $invalidJson);
        $this->assertTrue($result);
    }

    public function testOnMessageWithMissingClass()
    {
        $jsonWithoutClass = json_encode([
            'method' => 'get',
            'args' => [['id' => 1]]
        ]);

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $jsonWithoutClass);
        $this->assertTrue($result);
    }

    public function testOnMessageWithMissingMethod()
    {
        $jsonWithoutMethod = json_encode([
            'class' => 'user',
            'args' => [['id' => 1]]
        ]);

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $jsonWithoutMethod);
        $this->assertTrue($result);
    }

    public function testOnMessageWithEmptyArgs()
    {
        $jsonWithEmptyArgs = json_encode([
            'class' => 'user',
            'method' => 'get',
            'args' => []
        ]);

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $jsonWithEmptyArgs);
        $this->assertTrue($result);
    }

    public function testOnMessageWithMalformedData()
    {
        $malformedData = '{"class": "user", "method": "get", "args": [[id": 1]]'; // 缺少引号

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $malformedData);
        $this->assertTrue($result);
    }

    public function testOnMessageWithSpecialCharacters()
    {
        $jsonWithSpecialChars = json_encode([
            'class' => 'user',
            'method' => 'get',
            'args' => [['name' => '测试用户']]
        ]);

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $jsonWithSpecialChars);
        $this->assertTrue($result);
    }

    public function testOnMessageWithLargeData()
    {
        $largeData = str_repeat('a', 10000);
        $jsonWithLargeData = json_encode([
            'class' => 'user',
            'method' => 'get',
            'args' => [['data' => $largeData]]
        ]);

        // 模拟连接发送方法
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = $this->protocol->onMessage($this->mockConnection, $jsonWithLargeData);
        $this->assertTrue($result);
    }
}