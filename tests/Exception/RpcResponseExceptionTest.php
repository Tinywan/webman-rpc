<?php

namespace Tinywan\Rpc\Tests\Exception;

use Tinywan\Rpc\Error;
use Tinywan\Rpc\Exception\RpcResponseException;
use PHPUnit\Framework\TestCase;

class RpcResponseExceptionTest extends TestCase
{
    public function testExceptionConstruction()
    {
        $error = Error::make(500, 'Internal Server Error', ['details' => 'Database connection failed']);
        $exception = new RpcResponseException($error);
        
        $this->assertInstanceOf(RpcResponseException::class, $exception);
        $this->assertEquals(500, $exception->getCode());
        $this->assertEquals('Internal Server Error', $exception->getMessage());
    }

    public function testGetError()
    {
        $error = Error::make(404, 'Not Found', ['resource' => 'user']);
        $exception = new RpcResponseException($error);
        
        $returnedError = $exception->getError();
        $this->assertInstanceOf(Error::class, $returnedError);
        $this->assertEquals(404, $returnedError->getCode());
        $this->assertEquals('Not Found', $returnedError->getMessage());
        $this->assertEquals(['resource' => 'user'], $returnedError->getData());
    }

    public function testExceptionWithZeroCode()
    {
        $error = Error::make(0, 'Success');
        $exception = new RpcResponseException($error);
        
        $this->assertEquals(0, $exception->getCode());
        $this->assertEquals('Success', $exception->getMessage());
    }

    public function testExceptionWithNegativeCode()
    {
        $error = Error::make(-1, 'Custom Error');
        $exception = new RpcResponseException($error);
        
        $this->assertEquals(-1, $exception->getCode());
        $this->assertEquals('Custom Error', $exception->getMessage());
    }

    public function testExceptionWithEmptyMessage()
    {
        $error = Error::make(400, '');
        $exception = new RpcResponseException($error);
        
        $this->assertEquals(400, $exception->getCode());
        $this->assertEquals('', $exception->getMessage());
    }

    public function testExceptionInheritance()
    {
        $error = Error::make(500, 'Server Error');
        $exception = new RpcResponseException($error);
        
        $this->assertInstanceOf(\Exception::class, $exception);
    }
}