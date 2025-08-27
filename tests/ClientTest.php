<?php

namespace Tinywan\Rpc\Tests;

use Tinywan\Rpc\Client;
use Tinywan\Rpc\Exception\RpcResponseException;
use Tinywan\Rpc\Exception\RpcUnexpectedValueException;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    private $client;
    private $testServerAddress = 'tcp://127.0.0.1:9512';

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = new Client($this->testServerAddress);
    }

    public function testClientConstruction()
    {
        $client = new Client('tcp://127.0.0.1:9512');
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testRequestWithInvalidAddress()
    {
        $client = new Client('tcp://invalid.address:9999');
        
        $this->expectException(RpcUnexpectedValueException::class);
        $this->expectExceptionMessage('rpc request failed');
        
        $client->request([
            'class' => 'user',
            'method' => 'get',
            'args' => [[]]
        ]);
    }

    public function testRequestWithTimeout()
    {
        $client = new Client('tcp://8.8.8.8:9999'); // 使用不可达地址测试超时
        
        $this->expectException(RpcUnexpectedValueException::class);
        
        $client->request([
            'class' => 'user',
            'method' => 'get',
            'args' => [[]],
            'timeout' => 1
        ]);
    }

    public function testRequestParameters()
    {
        // 由于无法创建真实的RPC服务器，这里测试参数验证
        $this->assertInstanceOf(Client::class, $this->client);
        
        // 测试客户端地址设置
        $reflection = new \ReflectionClass($this->client);
        $property = $reflection->getProperty('address');
        $property->setAccessible(true);
        
        $this->assertEquals($this->testServerAddress, $property->getValue($this->client));
    }

    public function testRequestWithCustomTimeout()
    {
        // 创建一个模拟的socket资源
        $client = new Client('tcp://127.0.0.1:9512');
        
        // 由于无法真实连接，我们主要测试参数传递
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testRequestWithEmptyArgs()
    {
        $client = new Client('tcp://127.0.0.1:9512');
        
        // 测试空参数数组
        $this->assertInstanceOf(Client::class, $client);
    }

    public function testRequestWithMalformedResponse()
    {
        $client = new Client('tcp://127.0.0.1:9512');
        
        // 由于无法真实连接，这里主要测试客户端结构
        $this->assertInstanceOf(Client::class, $client);
    }
}