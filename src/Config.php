<?php
/**
 * @desc 配置缓存管理
 * @author Tinywan(ShaoBo Wan)
 * @email 756684177@qq.com
 * @date 2022/3/20 0:36
 */

declare(strict_types=1);

namespace Tinywan\Rpc;

class Config
{
    /**
     * 配置缓存
     * @var array
     */
    private static array $configCache = [];

    /**
     * 获取配置
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    public static function get(string $key = '', $default = null)
    {
        $cacheKey = 'plugin.tinywan.rpc.app';
        
        // 如果配置未缓存，则读取并缓存
        if (!isset(self::$configCache[$cacheKey])) {
            self::$configCache[$cacheKey] = config($cacheKey);
        }
        
        $config = self::$configCache[$cacheKey];
        
        // 如果没有指定key，返回整个配置
        if (empty($key)) {
            return $config;
        }
        
        // 支持点号分隔的key，如 'server.namespace'
        $keys = explode('.', $key);
        $value = $config;
        
        foreach ($keys as $k) {
            if (is_array($value) && isset($value[$k])) {
                $value = $value[$k];
            } else {
                return $default;
            }
        }
        
        return $value;
    }

    /**
     * 清除配置缓存
     * @return void
     */
    public static function clearCache(): void
    {
        self::$configCache = [];
    }

    /**
     * 获取服务器配置
     * @return array
     */
    public static function getServerConfig(): array
    {
        return self::get('server', []);
    }

    /**
     * 获取连接超时时间
     * @return int
     */
    public static function getConnectTimeout(): int
    {
        return (int)self::get('connect_timeout', 5);
    }

    /**
     * 获取请求超时时间
     * @return int
     */
    public static function getRequestTimeout(): int
    {
        return (int)self::get('request_timeout', 5);
    }

    /**
     * 获取命名空间
     * @return string
     */
    public static function getNamespace(): string
    {
        return self::get('server.namespace', 'service\\');
    }

    /**
     * 获取监听地址
     * @return string
     */
    public static function getListenAddress(): string
    {
        return self::get('server.listen_text_address', 'text://0.0.0.0:9512');
    }

    /**
     * 检查是否启用
     * @return bool
     */
    public static function isEnabled(): bool
    {
        return (bool)self::get('enable', true);
    }
}