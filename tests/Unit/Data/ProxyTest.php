<?php

use Weijiajia\HttpProxyManager\Data\Proxy;

test('代理可以使用基本属性创建', function () {
    $proxy = makeProxy();
    
    expect($proxy->getHost())->toBe('192.168.1.1');
    expect($proxy->getPort())->toBe(8080);
    expect($proxy->getType())->toBe('http');
    expect($proxy->getUsername())->toBeNull();
    expect($proxy->getPassword())->toBeNull();
    expect($proxy->isValid())->toBeTrue();
});

test('代理可以设置认证信息', function () {
    $proxy = makeProxy([
        'username' => 'user',
        'password' => 'pass',
    ]);
    
    expect($proxy->getUsername())->toBe('user');
    expect($proxy->getPassword())->toBe('pass');
});

test('代理可以检查有效期', function () {
    // 创建一个过期的代理
    $expiredProxy = makeProxy([
        'expiresAt' => new \DateTime('-1 day')
    ]);
    
    // 创建一个未过期的代理
    $validProxy = makeProxy([
        'expiresAt' => new \DateTime('+1 day')
    ]);
    
    expect($expiredProxy->isValid())->toBeFalse();
    expect($validProxy->isValid())->toBeTrue();
});

test('代理可以转换为数组', function () {
    $proxy = makeProxy([
        'username' => 'user',
        'password' => 'pass',
    ]);
    
    $array = $proxy->toArray();
    
    expect($array)->toBeArray()
        ->toHaveKey('host', '192.168.1.1')
        ->toHaveKey('port', 8080)
        ->toHaveKey('type', 'http')
        ->toHaveKey('username', 'user')
        ->toHaveKey('password', 'pass');
});