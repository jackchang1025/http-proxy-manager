<?php

use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\ProxyFormat;



// 测试组
uses()->group('proxy', 'data');

// 基本构造函数和属性测试
test('可以使用基本属性创建代理', function () {
    $proxy = makeProxy();
    
    expect($proxy->getHost())->toBe('192.168.1.1')
        ->and($proxy->getPort())->toBe(8080)
        ->and($proxy->getProtocol())->toBe('http')
        ->and($proxy->getUsername())->toBeNull()
        ->and($proxy->getPassword())->toBeNull()
        ->and($proxy->isValid())->toBeTrue();
});

test('可以创建带有所有属性的代理', function () {
    $expiresAt = new \DateTime('+1 day');
    $metadata = ['source' => 'test', 'region' => 'US'];
    $url = 'socks5://testuser:testpass@10.0.0.1:9090';
    
    $proxy = new Proxy(
        host: '10.0.0.1',
        port: 9090,
        protocol: 'socks5',
        username: 'testuser',
        password: 'testpass',
        url: $url,
        expiresAt: $expiresAt,
        metadata: $metadata
    );
    
    expect($proxy->getHost())->toBe('10.0.0.1')
        ->and($proxy->getPort())->toBe(9090)
        ->and($proxy->getProtocol())->toBe('socks5')
        ->and($proxy->getUsername())->toBe('testuser')
        ->and($proxy->getPassword())->toBe('testpass')
        ->and($proxy->getUrl())->toBe($url)
        ->and($proxy->getExpiresAt())->toBe($expiresAt)
        ->and($proxy->getMetadata())->toBe($metadata);
});

// 测试认证信息
test('代理可以设置认证信息', function () {
    $proxy = makeProxy([
        'username' => 'user',
        'password' => 'pass',
    ]);
    
    expect($proxy->getUsername())->toBe('user')
        ->and($proxy->getPassword())->toBe('pass');
});

// 测试代理有效性
test('代理可以检查有效期 - 未过期', function () {
    $validProxy = makeProxy([
        'expiresAt' => new \DateTime('+1 day')
    ]);
    
    expect($validProxy->isValid())->toBeTrue();
});

test('代理可以检查有效期 - 已过期', function () {
    $expiredProxy = makeProxy([
        'expiresAt' => new \DateTime('-1 day')
    ]);
    
    expect($expiredProxy->isValid())->toBeFalse();
});

test('代理没有设置过期时间时始终有效', function () {
    $proxy = makeProxy([
        'expiresAt' => null
    ]);
    
    expect($proxy->isValid())->toBeTrue();
});

// 测试toArray方法
test('代理可以转换为数组', function () {
    $expiresAt = new \DateTime('+1 day');
    $metadata = ['source' => 'test'];
    
    $proxy = new Proxy(
        host: '192.168.1.1',
        port: 8080, 
        protocol: 'http',
        username: 'user',
        password: 'pass',
        expiresAt: $expiresAt,
        metadata: $metadata
    );
    
    $array = $proxy->toArray();
    
    expect($array)->toBeArray()
        ->toHaveKey('host', '192.168.1.1')
        ->toHaveKey('port', 8080)
        ->toHaveKey('protocol', 'http')
        ->toHaveKey('username', 'user')
        ->toHaveKey('password', 'pass')
        ->toHaveKey('expiresAt', $expiresAt)
        ->toHaveKey('metadata', $metadata);
});

// 测试from静态方法
test('可以通过from静态方法创建代理', function () {
    $data = [
        'host' => '10.0.0.1',
        'port' => 9090,
        'protocol' => 'socks5',
        'username' => 'testuser',
        'password' => 'testpass'
    ];
    
    $proxy = Proxy::from($data);
    
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('10.0.0.1')
        ->and($proxy->getPort())->toBe(9090)
        ->and($proxy->getProtocol())->toBe('socks5')
        ->and($proxy->getUsername())->toBe('testuser')
        ->and($proxy->getPassword())->toBe('testpass');
});








// 测试setter方法
test('可以设置过期时间', function () {
    $proxy = makeProxy();
    $expiresAt = new \DateTime('+2 days');
    
    $result = $proxy->setExpiresAt($expiresAt);
    
    expect($result)->toBe($proxy) // 测试链式调用返回自身
        ->and($proxy->getExpiresAt())->toBe($expiresAt);
});

test('可以设置元数据', function () {
    $proxy = makeProxy();
    $metadata = ['region' => 'EU', 'tags' => ['residential', 'mobile']];
    
    $result = $proxy->setMetadata($metadata);
    
    expect($result)->toBe($proxy) // 测试链式调用返回自身
        ->and($proxy->getMetadata())->toBe($metadata);
});
