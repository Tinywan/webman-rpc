<?php

namespace Tinywan\Rpc\Tests\Exception;

use Tinywan\Rpc\Exception\RpcUnexpectedValueException;
use PHPUnit\Framework\TestCase;

class RpcUnexpectedValueExceptionTest extends TestCase
{
    public function testExceptionConstruction()
    {
        $exception = new RpcUnexpectedValueException('Test message');
        
        $this->assertInstanceOf(RpcUnexpectedValueException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(0, $exception->getCode());
    }

    public function testExceptionWithCode()
    {
        $exception = new RpcUnexpectedValueException('Test message', 500);
        
        $this->assertInstanceOf(RpcUnexpectedValueException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertEquals(500, $exception->getCode());
    }

    public function testExceptionWithPrevious()
    {
        $previous = new \Exception('Previous exception');
        $exception = new RpcUnexpectedValueException('Test message', 0, $previous);
        
        $this->assertInstanceOf(RpcUnexpectedValueException::class, $exception);
        $this->assertEquals('Test message', $exception->getMessage());
        $this->assertSame($previous, $exception->getPrevious());
    }

    public function testExceptionInheritance()
    {
        $exception = new RpcUnexpectedValueException('Test message');
        
        $this->assertInstanceOf(\UnexpectedValueException::class, $exception);
        $this->assertInstanceOf(\Exception::class, $exception);
    }

    public function testExceptionWithEmptyMessage()
    {
        $exception = new RpcUnexpectedValueException('');
        
        $this->assertInstanceOf(RpcUnexpectedValueException::class, $exception);
        $this->assertEquals('', $exception->getMessage());
    }

    public function testExceptionWithSpecialCharacters()
    {
        $message = '测试消息 🎉';
        $exception = new RpcUnexpectedValueException($message);
        
        $this->assertInstanceOf(RpcUnexpectedValueException::class, $exception);
        $this->assertEquals($message, $exception->getMessage());
    }
}