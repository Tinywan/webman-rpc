<?php

namespace Tinywan\Rpc\Tests;

use Tinywan\Rpc\Config;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // 清空配置缓存
        Config::clearCache();
        
        // 模拟配置函数
        if (!function_exists('config')) {
            function config($key) {
                $config = [
                    'enable' => true,
                    'server' => [
                        'namespace' => 'service\\',
                        'listen_text_address' => 'text://0.0.0.0:9512',
                    ],
                    'connect_timeout' => 5,
                    'request_timeout' => 5,
                ];
                
                return $config[$key] ?? null;
            }
        }
    }

    public function testGetConfig()
    {
        $config = Config::get();
        $this->assertTrue($config['enable']);
        $this->assertEquals('service\\', $config['server']['namespace']);
    }

    public function testGetSpecificConfig()
    {
        $enable = Config::get('enable');
        $this->assertTrue($enable);
        
        $namespace = Config::get('server.namespace');
        $this->assertEquals('service\\', $namespace);
    }

    public function testGetConfigWithDefault()
    {
        $nonexistent = Config::get('nonexistent.key', 'default_value');
        $this->assertEquals('default_value', $nonexistent);
    }

    public function testGetNamespace()
    {
        $namespace = Config::getNamespace();
        $this->assertEquals('service\\', $namespace);
    }

    public function testGetConnectTimeout()
    {
        $timeout = Config::getConnectTimeout();
        $this->assertEquals(5, $timeout);
        $this->assertIsInt($timeout);
    }

    public function testGetRequestTimeout()
    {
        $timeout = Config::getRequestTimeout();
        $this->assertEquals(5, $timeout);
        $this->assertIsInt($timeout);
    }

    public function testGetListenAddress()
    {
        $address = Config::getListenAddress();
        $this->assertEquals('text://0.0.0.0:9512', $address);
    }

    public function testIsEnabled()
    {
        $this->assertTrue(Config::isEnabled());
    }

    public function testGetServerConfig()
    {
        $serverConfig = Config::getServerConfig();
        $this->assertIsArray($serverConfig);
        $this->assertEquals('service\\', $serverConfig['namespace']);
        $this->assertEquals('text://0.0.0.0:9512', $serverConfig['listen_text_address']);
    }

    public function testClearCache()
    {
        // 第一次获取配置
        $config1 = Config::get();
        
        // 清空缓存
        Config::clearCache();
        
        // 重新获取配置
        $config2 = Config::get();
        
        // 配置应该相同
        $this->assertEquals($config1, $config2);
    }

    public function testConfigCaching()
    {
        // 第一次获取配置
        $config1 = Config::get();
        
        // 修改模拟配置函数
        global $mockConfigCallCount;
        $mockConfigCallCount = 0;
        
        // 第二次获取配置应该从缓存读取
        $config2 = Config::get();
        
        // 配置应该相同
        $this->assertEquals($config1, $config2);
    }
}