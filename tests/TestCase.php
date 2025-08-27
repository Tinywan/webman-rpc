<?php

namespace Tinywan\Rpc\Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 模拟配置函数
        if (!function_exists('config')) {
            function config($key) {
                return [
                    'enable' => true,
                    'server' => [
                        'namespace' => 'service\\',
                        'listen_text_address' => 'text://0.0.0.0:9512',
                    ],
                    'connect_timeout' => 5,
                    'request_timeout' => 5,
                ][$key] ?? null;
            }
        }
    }
}