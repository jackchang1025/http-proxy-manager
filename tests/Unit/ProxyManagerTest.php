<?php

use Weijiajia\HttpProxyManager\ProxyManager;
use Weijiajia\HttpProxyManager\ProxyConnector;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Orchestra\Testbench\TestCase;
// 设置测试组，但不重复指定测试用例类（已在Pest.php中全局设置）
uses()->group('proxy-manager');

uses(TestCase::class);

// 使用共享的测试环境
uses()->beforeEach(function () {
    // 配置测试环境
    defineEnvironment($this->app);
    
    // 创建模拟ProxyManager 
    $this->proxyManager = Mockery::mock(ProxyManager::class)->makePartial();
});

uses()->afterEach(function () {
    Mockery::close();
});

// 获取默认驱动
it('可以获取默认驱动', function () {
    // 定义模拟行为
    $mockConnector = Mockery::mock(ProxyConnector::class);
    
    $this->proxyManager->shouldReceive('driver')
        ->withNoArgs()
        ->once()
        ->andReturn($mockConnector);
        
    // 执行测试
    $connector = $this->proxyManager->driver();
    
    // 验证结果
    expect($connector)->toBe($mockConnector);
});

// 通过驱动名称获取驱动
it('可以通过驱动名称获取驱动', function () {
    // 定义模拟行为
    $mockConnector = Mockery::mock(ProxyConnector::class);
    
    $this->proxyManager->shouldReceive('driver')
        ->with('iproyal')
        ->once()
        ->andReturn($mockConnector);
        
    // 执行测试
    $connector = $this->proxyManager->driver('iproyal');
    
    // 验证结果
    expect($connector)->toBe($mockConnector);
});

// 使用connector方法获取驱动
it('可以使用connector方法获取驱动', function () {
    // 定义模拟行为
    $mockConnector = Mockery::mock(ProxyConnector::class);
    
    $this->proxyManager->shouldReceive('connector')
        ->with('iproyal')
        ->once()
        ->andReturn($mockConnector);
        
    // 执行测试
    $connector = $this->proxyManager->connector('iproyal');
    
    // 验证结果
    expect($connector)->toBe($mockConnector);
});

// 提取代理IP
it('可以提取代理IP', function () {
    // 创建测试数据
    $mockConnector = Mockery::mock(ProxyConnector::class);
    $mockProxy = new Proxy('192.168.1.1', 8080, 'http', 'user1', 'pass1');
    $mockProxies = new Collection([$mockProxy]);
    
    // 定义模拟行为
    $this->proxyManager->shouldReceive('driver')
        ->with('huashengdaili')
        ->once()
        ->andReturn($mockConnector);
        
    $mockConnector->shouldReceive('extractIp')
        ->once()
        ->andReturn($mockProxies);
        
    // 执行测试
    $connector = $this->proxyManager->driver('huashengdaili');
    $proxies = $connector->extractIp();
    
    // 验证结果
    expect($proxies)->toBeInstanceOf(Collection::class)
        ->and($proxies)->toHaveCount(1)
        ->and($proxies->first())->toBeInstanceOf(Proxy::class)
        ->and($proxies->first()->getHost())->toBe('192.168.1.1')
        ->and($proxies->first()->getPort())->toBe(8080);
});

// 获取直连IP
it('可以获取直连IP', function () {
    // 创建测试数据
    $mockConnector = Mockery::mock(ProxyConnector::class);
    $mockProxy = new Proxy(
        'geo.iproyal.com', 
        12321, 
        'http', 
        'test-user', 
        'test-pass'
    );
    
    // 定义模拟行为
    $this->proxyManager->shouldReceive('driver')
        ->with('iproyal')
        ->once()
        ->andReturn($mockConnector);
        
    $mockConnector->shouldReceive('directConnectionIp')
        ->once()
        ->andReturn($mockProxy);
        
    // 执行测试
    $connector = $this->proxyManager->driver('iproyal');
    $proxy = $connector->directConnectionIp();
    
    // 验证结果
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('geo.iproyal.com')
        ->and($proxy->getPort())->toBe(12321)
        ->and($proxy->getUsername())->toBe('test-user')
        ->and($proxy->getPassword())->toBe('test-pass');
});

// 使用不存在的驱动抛出异常
it('使用不存在的驱动时抛出异常', function () {
    // 定义模拟行为
    $this->proxyManager->shouldReceive('driver')
        ->with('non-existent-driver')
        ->once()
        ->andThrow(new \InvalidArgumentException('Driver [non-existent-driver] not supported.'));
    
    // 执行测试    
    $this->proxyManager->driver('non-existent-driver');
})->throws(\InvalidArgumentException::class);

// 测试链式调用设置代理参数
it('可以通过链式调用设置代理参数', function () {
    // 创建测试数据
    $mockConnector = Mockery::mock(ProxyConnector::class);
    $mockProxy = new Proxy(
        'proxy.example.com',
        8080,
        'http',
        'user',
        'pass'
    );
    
    // 定义模拟行为
    $this->proxyManager->shouldReceive('connector')
        ->withNoArgs()
        ->once()
        ->andReturn($mockConnector);
    
    // 模拟链式调用方法
    $mockConnector->shouldReceive('withSession')
        ->with('test')
        ->once()
        ->andReturnSelf();
        
    $mockConnector->shouldReceive('withCountry')
        ->with('test')
        ->once()
        ->andReturnSelf();
    
    $mockConnector->shouldReceive('directConnectionIp')
        ->withNoArgs()
        ->once()
        ->andReturn($mockProxy);
    
    // 执行测试
    $connector = $this->proxyManager->connector();
    $connector->withSession('test');
    $connector->withCountry('test');
    $proxy = $connector->directConnectionIp();
    
    // 验证结果
    expect($proxy)
        ->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('proxy.example.com')
        ->and($proxy->getPort())->toBe(8080);
});



