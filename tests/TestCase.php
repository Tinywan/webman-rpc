<?php

declare(strict_types=1);

namespace Tinywan\Rpc\Tests;

use Mockery as m;
use PHPUnit\Framework\TestCase as BaseTestCase;
use Webman\Config as WebmanConfig;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $ref = new \ReflectionProperty(WebmanConfig::class, 'config');
        $ref->setAccessible(true);
        $ref->setValue(null, [
            'plugin' => [
                'tinywan' => [
                    'rpc' => [
                        'app' => [
                            'enable' => true,
                            'server' => [
                                'namespace' => 'service\\',
                                'listen_text_address' => 'text://0.0.0.0:9512',
                            ],
                            'connect_timeout' => 5,
                            'request_timeout' => 5,
                        ],
                    ],
                    'exception-handler' => [
                        'app' => [
                            'enable' => true,
                            'event_trigger' => [
                                'dingtalk' => [
                                    'accessToken' => 'fake_token',
                                    'secret' => 'fake_secret',
                                    'title' => 'test',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        \Tinywan\Rpc\Config::clearCache();
    }

    protected function tearDown(): void
    {
        if (class_exists(m::class)) {
            m::close();
        }

        parent::tearDown();
    }
}
