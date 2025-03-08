<?php

namespace Tests;

use PHPUnit\Framework\TestCase as BaseTestCase;
use Weijiajia\HttpProxyManager\Data\Proxy;

abstract class TestCase extends BaseTestCase
{
    /**
     * 创建一个测试用的代理对象
     */
    protected function makeTestProxy(array $overrides = []): Proxy
    {
        $defaults = [
            'host' => '192.168.1.1',
            'port' => 8080,
            'type' => 'http',
            'username' => null,
            'password' => null,
            'expiresAt' => null
        ];

        $data = array_merge($defaults, $overrides);
        
        return new Proxy(
            host: $data['host'],
            port: $data['port'],
            type: $data['type'],
            username: $data['username'],
            password: $data['password'],
            expiresAt: $data['expiresAt']
        );
    }
}
