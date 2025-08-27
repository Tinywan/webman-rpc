<?php

namespace Tinywan\Rpc\Tests;

use Tinywan\Rpc\JsonParser;
use PHPUnit\Framework\TestCase;
use Mockery as m;

class JsonParserTest extends TestCase
{
    private $mockConnection;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockConnection = m::mock('Workerman\Connection\TcpConnection');
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }

    public function testConstants()
    {
        $this->assertEquals('1.0', JsonParser::VERSION);
        $this->assertEquals('@', JsonParser::DELIMITER);
        $this->assertEquals(32700, JsonParser::PARSER_ERROR);
        $this->assertEquals(32600, JsonParser::INVALID_REQUEST);
        $this->assertEquals(32601, JsonParser::METHOD_NOT_FOUND);
        $this->assertEquals(32602, JsonParser::INVALID_PARAMS);
        $this->assertEquals(32603, JsonParser::INTERNAL_ERROR);
    }

    public function testEncodeWithBasicData()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, 'Success', ['data' => 'test']);
        $this->assertTrue($result);
    }

    public function testEncodeWithoutData()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 404, 'Not Found');
        $this->assertTrue($result);
    }

    public function testEncodeWithEmptyMessage()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 0, '');
        $this->assertTrue($result);
    }

    public function testEncodeWithComplexData()
    {
        $complexData = [
            'user' => ['id' => 1, 'name' => 'test'],
            'items' => [['id' => 1, 'name' => 'item1'], ['id' => 2, 'name' => 'item2']],
            'meta' => ['total' => 2, 'page' => 1]
        ];

        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, 'Success', $complexData);
        $this->assertTrue($result);
    }

    public function testEncodeWithSpecialCharacters()
    {
        $specialData = ['message' => '测试消息', 'emoji' => '🎉'];

        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, 'Success', $specialData);
        $this->assertTrue($result);
    }

    public function testEncodeWithUnicodeData()
    {
        $unicodeData = ['text' => 'Hello 世界 🌍'];

        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, 'Success', $unicodeData);
        $this->assertTrue($result);
    }

    public function testEncodeWithErrorCode()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 500, 'Internal Server Error');
        $this->assertTrue($result);
    }

    public function testEncodeWithZeroCode()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 0, 'Success');
        $this->assertTrue($result);
    }

    public function testEncodeWithNegativeCode()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, -1, 'Custom Error');
        $this->assertTrue($result);
    }

    public function testEncodeWithVeryLongMessage()
    {
        $longMessage = str_repeat('This is a very long message. ', 1000);
        
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, $longMessage);
        $this->assertTrue($result);
    }

    public function testEncodeWithNumericData()
    {
        $numericData = ['count' => 42, 'price' => 19.99, 'is_active' => true];

        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, 'Success', $numericData);
        $this->assertTrue($result);
    }

    public function testEncodeWithNullData()
    {
        $this->mockConnection->shouldReceive('send')->andReturn(true);

        $result = JsonParser::encode($this->mockConnection, 200, 'Success', null);
        $this->assertTrue($result);
    }
}