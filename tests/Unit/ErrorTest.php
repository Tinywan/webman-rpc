<?php

declare(strict_types=1);

use Tinywan\Rpc\Error;

it('can make an Error instance with code, message and data', function () {
    $error = Error::make(400, 'Bad Request', ['field' => 'username']);

    expect($error)->toBeInstanceOf(Error::class)
        ->and($error->getCode())->toBe(400)
        ->and($error->getMessage())->toBe('Bad Request')
        ->and($error->getData())->toBe(['field' => 'username']);
});

it('can serialize to JSON correctly via JsonSerializable', function () {
    $error = Error::make(500, 'Internal Server Error', ['detail' => 'trace']);
    $serialized = $error->jsonSerialize();

    expect($serialized)->toBeArray()
        ->and($serialized)->toHaveKey('code', 500)
        ->and($serialized)->toHaveKey('message', 'Internal Server Error')
        ->and($serialized)->toHaveKey('data', ['detail' => 'trace']);

    $json = json_encode($error);
    expect(json_decode($json, true))->toBe([
        'code' => 500,
        'message' => 'Internal Server Error',
        'data' => ['detail' => 'trace'],
    ]);
});

it('supports null data in Error::make', function () {
    $error = Error::make(0, 'Success');

    expect($error->getCode())->toBe(0)
        ->and($error->getMessage())->toBe('Success')
        ->and($error->getData())->toBeNull();
});
