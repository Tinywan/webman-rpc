<?php

namespace Tinywan\Rpc\Tests;

use Tinywan\Rpc\Error;
use PHPUnit\Framework\TestCase;

class ErrorTest extends TestCase
{
    public function testErrorCreation()
    {
        $error = Error::make(400, 'Bad Request', ['field' => 'value']);
        
        $this->assertInstanceOf(Error::class, $error);
        $this->assertEquals(400, $error->getCode());
        $this->assertEquals('Bad Request', $error->getMessage());
        $this->assertEquals(['field' => 'value'], $error->getData());
    }

    public function testErrorCreationWithoutData()
    {
        $error = Error::make(404, 'Not Found');
        
        $this->assertInstanceOf(Error::class, $error);
        $this->assertEquals(404, $error->getCode());
        $this->assertEquals('Not Found', $error->getMessage());
        $this->assertNull($error->getData());
    }

    public function testErrorGetters()
    {
        $error = Error::make(500, 'Internal Server Error', ['error' => 'database']);
        
        $this->assertEquals(500, $error->getCode());
        $this->assertEquals('Internal Server Error', $error->getMessage());
        $this->assertEquals(['error' => 'database'], $error->getData());
    }

    public function testErrorJsonSerialization()
    {
        $error = Error::make(403, 'Forbidden', ['reason' => 'invalid_token']);
        
        $expected = [
            'code' => 403,
            'message' => 'Forbidden',
            'data' => ['reason' => 'invalid_token']
        ];
        
        $this->assertEquals($expected, $error->jsonSerialize());
    }

    public function testErrorJsonSerializationWithoutData()
    {
        $error = Error::make(200, 'OK');
        
        $expected = [
            'code' => 200,
            'message' => 'OK',
            'data' => null
        ];
        
        $this->assertEquals($expected, $error->jsonSerialize());
    }

    public function testErrorWithEmptyMessage()
    {
        $error = Error::make(0, '');
        
        $this->assertEquals(0, $error->getCode());
        $this->assertEquals('', $error->getMessage());
    }

    public function testErrorWithZeroCode()
    {
        $error = Error::make(0, 'Success');
        
        $this->assertEquals(0, $error->getCode());
        $this->assertEquals('Success', $error->getMessage());
    }

    public function testErrorWithNegativeCode()
    {
        $error = Error::make(-1, 'Custom Error');
        
        $this->assertEquals(-1, $error->getCode());
        $this->assertEquals('Custom Error', $error->getMessage());
    }

    public function testErrorWithComplexData()
    {
        $complexData = [
            'user' => ['id' => 1, 'name' => 'test'],
            'errors' => ['field1' => 'required', 'field2' => 'invalid'],
            'meta' => ['timestamp' => time()]
        ];
        
        $error = Error::make(422, 'Validation Error', $complexData);
        
        $this->assertEquals(422, $error->getCode());
        $this->assertEquals('Validation Error', $error->getMessage());
        $this->assertEquals($complexData, $error->getData());
    }

    public function testErrorImplementsJsonSerializable()
    {
        $error = Error::make(400, 'Bad Request');
        
        $this->assertInstanceOf(\JsonSerializable::class, $error);
    }
}