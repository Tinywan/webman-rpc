<?php

declare(strict_types=1);

use Tinywan\Rpc\Client;
use Tinywan\Rpc\Exception\RpcUnexpectedValueException;

it('instantiates Client with address correctly', function () {
    $client = new Client('tcp://127.0.0.1:9512');

    expect($client)->toBeInstanceOf(Client::class);

    $reflection = new \ReflectionClass($client);
    $property = $reflection->getProperty('address');
    $property->setAccessible(true);

    expect($property->getValue($client))->toBe('tcp://127.0.0.1:9512');
});

it('throws RpcUnexpectedValueException when connecting to unreachable host', function () {
    $client = new Client('tcp://invalid.address:9999');

    expect(function () use ($client) {
        $client->request([
            'class' => 'user',
            'method' => 'get',
            'args' => [['id' => 1]],
            'timeout' => 1,
        ]);
    })->toThrow(RpcUnexpectedValueException::class);
});
