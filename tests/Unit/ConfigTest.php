<?php

declare(strict_types=1);

use Tinywan\Rpc\Config;

beforeEach(function () {
    Config::clearCache();
});

it('can get entire configuration array', function () {
    $config = Config::get();

    expect($config)->toBeArray()
        ->and($config['enable'])->toBeTrue()
        ->and($config['server']['namespace'])->toBe('service\\');
});

it('can get specific config by key and dot notation', function () {
    expect(Config::get('enable'))->toBeTrue()
        ->and(Config::get('server.namespace'))->toBe('service\\')
        ->and(Config::get('server.listen_text_address'))->toBe('text://0.0.0.0:9512');
});

it('returns default value for nonexistent key', function () {
    expect(Config::get('nonexistent.key', 'default_value'))->toBe('default_value')
        ->and(Config::get('server.nonexistent', 123))->toBe(123);
});

it('can get server config array', function () {
    $serverConfig = Config::getServerConfig();

    expect($serverConfig)->toBeArray()
        ->and($serverConfig['namespace'])->toBe('service\\')
        ->and($serverConfig['listen_text_address'])->toBe('text://0.0.0.0:9512');
});

it('can get timeout settings', function () {
    expect(Config::getConnectTimeout())->toBe(5)
        ->and(Config::getRequestTimeout())->toBe(5);
});

it('can get namespace and listen address directly', function () {
    expect(Config::getNamespace())->toBe('service\\')
        ->and(Config::getListenAddress())->toBe('text://0.0.0.0:9512');
});

it('can clear cache and re-read config', function () {
    $config1 = Config::get();
    Config::clearCache();
    $config2 = Config::get();

    expect($config1)->toEqual($config2);
});
