<?php

use Weijiajia\HttpProxyManager\Drivers\IpRoyal\Request\ResidentialDirectConnectionIp;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Saloon\Http\Connector;

// 设置测试组
uses()->group('iproyal', 'request');

// 基本功能测试
it('creates request with basic parameters', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request)->toBeInstanceOf(ResidentialDirectConnectionIp::class);
});

// 测试生成会话 ID
it('generates session ID of correct length', function () {
    $reflection = new ReflectionClass(ResidentialDirectConnectionIp::class);
    $method = $reflection->getMethod('generateSessionId');
    $method->setAccessible(true);
    
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    $sessionId = $method->invoke($request);
    expect(strlen($sessionId))->toBe(8);
    
    $sessionId = $method->invoke($request, 16);
    expect(strlen($sessionId))->toBe(16);
});

// 测试sticky session会自动生成session ID和lifetime
it('automatically generates session ID and lifetime when sticky_session is true', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http',
            'sticky_session' => true,
        ]
    );

    
    expect($request->getOptions()['session'])->not->toBeNull()
        ->and($request->getOptions()['lifetime'])->toBe('10m');
});

// 测试传递自定义session优先于自动生成
it('uses provided session ID instead of generating one', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http',
            'sticky_session' => true,
            'session' => 'custom123'
        ]
    );
    
    expect($request->getOptions()['session'])->toBe('custom123');
});

// 测试getHost方法
it('returns correct host', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request->getHost())->toBe('geo.iproyal.com');
});

// 测试getPort方法
it('returns correct port', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request->getPort())->toBe(12321);
});

// 测试getUsername方法
it('returns correct username', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request->getUsername())->toBe('test-user');
});

// 测试getPassword方法返回buildPassword方法的结果
it('returns correct password', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request->getPassword())->toBe('test-pass');
});

// 测试getProtocol方法
it('returns correct protocol', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request->getProtocol())->toBe('http');
});

it('includes default parameters not in username  host port protocol', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http'
        ]
    );
    
    expect($request->getPassword())->toContain('test-pass')
        ->and($request->getPassword())->not->toContain('test-user')
        ->and($request->getPassword())->not->toContain('geo.iproyal.com')
        ->and($request->getPassword())->not->toContain('12321')
        ->and($request->getPassword())->not->toContain('http');
});

it('includes default and custom parameters in password', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http',
            'country' => 'US',
            'streaming' => '1',
            'forcerandom' => '1',
            'xxx' => '1',
        ]
    );
    
    expect($request->getPassword())->toContain('test-pass')
        ->and($request->getPassword())->toContain('country-US')
        ->and($request->getPassword())->toContain('streaming-1')
        ->and($request->getPassword())->toContain('xxx-1')
        ->and($request->getPassword())->toContain('forcerandom-1');
});

// 测试buildPassword方法 - 多个参数
it('builds password with multiple parameters', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http',
            'country' => 'US',
            'state' => 'California',
            'streaming' => 1
        ]
    );
    
    expect($request->getPassword())->toContain('test-pass')
        ->and($request->getPassword())->toContain('country-US')
        ->and($request->getPassword())->toContain('state-California')
        ->and($request->getPassword())->toContain('streaming-1');
});

// 测试完整的请求周期
it('creates correct proxy object', function () {
    $request = new ResidentialDirectConnectionIp(
        options: [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http',
            'country' => 'US'
        ]
    );
    
    // 创建一个简单的连接器来发送请求
    $connector = new class extends Connector {
        public function resolveBaseUrl(): string
        {
            return 'https://example.com';
        }
    };
    
    // 使用连接器发送请求
    $response = $connector->send($request);
    
    // 从响应创建DTO
    $proxy = $request->createDtoFromResponse($response);
    
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('geo.iproyal.com')
        ->and($proxy->getPort())->toBe(12321)
        ->and($proxy->getUsername())->toBe('test-user')
        ->and($proxy->getPassword())->toContain('test-pass')
        ->and($proxy->getPassword())->toContain('country-US')
        ->and($proxy->getProtocol())->toBe('http');
}); 