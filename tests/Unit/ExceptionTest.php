<?php

declare(strict_types=1);

use Tinywan\Rpc\Error;
use Tinywan\Rpc\Exception\RpcResponseException;
use Tinywan\Rpc\Exception\RpcUnexpectedValueException;

it('creates RpcResponseException wrapping an Error instance', function () {
    $error = Error::make(404, 'User Not Found', ['uid' => 123]);
    $exception = new RpcResponseException($error);

    expect($exception)->toBeInstanceOf(\Exception::class)
        ->and($exception->getMessage())->toBe('User Not Found')
        ->and($exception->getCode())->toBe(404)
        ->and($exception->getError())->toBe($error)
        ->and($exception->getError()->getData())->toBe(['uid' => 123]);
});

it('creates RpcUnexpectedValueException correctly', function () {
    $exception = new RpcUnexpectedValueException('Unexpected RPC response', 500);

    expect($exception)->toBeInstanceOf(\UnexpectedValueException::class)
        ->and($exception->getMessage())->toBe('Unexpected RPC response')
        ->and($exception->getCode())->toBe(500);
});
