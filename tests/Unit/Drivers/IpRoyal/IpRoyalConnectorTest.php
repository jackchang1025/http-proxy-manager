<?php

use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Drivers\IpRoyal\IpRoyalConnector;
use Weijiajia\HttpProxyManager\Exception\ProxyModelNotFoundException;

// 设置测试组
uses()->group('iproyal');

// 设置公共测试数据
beforeEach(function () {
    $this->config = [
        'mode' => 'direct_connection_ip',
        'username' => 'test-user',
        'password' => 'test-pass',
        'host' => 'geo.iproyal.com',
        'port' => 12321,
        'protocol' => 'http',
    ];

    $this->connector = new IpRoyalConnector($this->config);
});

// 测试基础 URL 解析
it('resolves correct base URL', function () {
    expect($this->connector->resolveBaseUrl())->toBe('https://api.iproyal.com/');
});

// 测试不支持提取 IP 功能
it('throws exception when trying to extract IPs', function () {
    $this->connector->extractIp();
})->throws(ProxyModelNotFoundException::class, 'IpRoyalConnector');

// 测试直连 IP 功能
it('creates direct connection IP with basic settings', function () {
    $config = [
        'username' => 'test-user',
        'password' => 'test-pass',
        'host' => 'geo.iproyal.com',
        'port' => 12321,
        'protocol' => 'http',
    ];

    $connector = new IpRoyalConnector($config);
    $proxy = $connector->directConnectionIp();

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('geo.iproyal.com')
        ->and($proxy->getPort())->toBe(12321)
        ->and($proxy->getUsername())->toBe('test-user')
        ->and($proxy->getPassword())->toContain('test-pass')
    ;
});

// 测试带国家过滤的直连 IP
it('creates direct connection IP with country filter', function () {
    $proxy = $this->connector->directConnectionIp([
        'country' => 'US',
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('test-pass')
        ->and($proxy->getPassword())->toContain('country-US')
    ;
});

// 测试带状态过滤的直连 IP
it('creates direct connection IP with state filter', function () {
    $proxy = $this->connector->directConnectionIp([
        'state' => 'California',
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('state-California')
    ;
});

// 测试带区域过滤的直连 IP
it('creates direct connection IP with region filter', function () {
    $proxy = $this->connector->directConnectionIp([
        'region' => 'West',
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('region-West')
    ;
});

// 测试带粘性会话的直连 IP
it('creates direct connection IP with sticky session', function () {
    $proxy = $this->connector->directConnectionIp([
        'sticky_session' => true,
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('session-')
        ->and($proxy->getPassword())->toContain('lifetime-10m')
    ;
});

// 测试自定义会话 ID
it('creates direct connection IP with custom session ID', function () {
    $proxy = $this->connector->directConnectionIp([
        'sticky_session' => true,
        'session' => 'custom123',
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('session-custom123')
    ;
});

// 测试优质代理池选项
it('creates direct connection IP with streaming option', function () {
    $proxy = $this->connector->directConnectionIp([
        'streaming' => 1,
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('streaming-1')
    ;
});

// 测试 ISP 静态跳过选项
it('creates direct connection IP with skip ISP static option', function () {
    $proxy = $this->connector->directConnectionIp([
        'skipispstatic' => 1,
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('skipispstatic-1')
    ;
});

// 测试 IP 跳过列表选项
it('creates direct connection IP with skip IPs list', function () {
    $proxy = $this->connector->directConnectionIp([
        'skipipslist' => 'list123',
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('skipipslist-list123')
    ;
});

// 测试禁用强制随机选项
it('creates direct connection IP with force random disabled', function () {
    $proxy = $this->connector->directConnectionIp([
        'forcerandom' => 0,
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('forcerandom-0')
    ;
});

// 测试组合多个选项
it('creates direct connection IP with multiple options', function () {
    $proxy = $this->connector->directConnectionIp([
        'country' => 'US',
        'state' => 'California',
        'sticky_session' => true,
        'streaming' => 1,
    ]);

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getPassword())->toContain('country-US')
        ->and($proxy->getPassword())->toContain('state-California')
        ->and($proxy->getPassword())->toContain('session-')
        ->and($proxy->getPassword())->toContain('streaming-1')
    ;
});

// 测试自定义协议
it('creates direct connection IP with socks5 protocol', function () {
    $config = [
        'username' => 'test-user',
        'password' => 'test-pass',
        'host' => 'geo.iproyal.com',
        'port' => 12321,
        'protocol' => 'socks5',
    ];

    $connector = new IpRoyalConnector($config);
    $proxy = $connector->directConnectionIp();

    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('geo.iproyal.com')
        ->and($proxy->getPort())->toBe(12321)
        ->and($proxy->getUsername())->toBe('test-user')
        ->and($proxy->getPassword())->toContain('test-pass')
        ->and($proxy->getProtocol())->toBe('socks5'); // 检查内部type属性
});
