<?php

use Weijiajia\HttpProxyManager\ProxyConnector;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Enums\Method;

// 模拟 ProxyConnector 具体实现
class TestProxyConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.test.com';
    }
}

// 模拟实现 AccountPasswordInterface
class TestAccountPasswordRequest extends Request implements AccountPasswordInterface
{
    protected Method $method = Method::GET;
    
    public function resolveEndpoint(): string
    {
        return '/account-password';
    }
    
    public function createDtoFromResponse(Response $response): Proxy
    {
        return makeProxy([
            'username' => 'test_user',
            'password' => 'test_pass'
        ]);
    }
}

// 模拟实现 DynamicInterface
class TestDynamicRequest extends Request implements DynamicInterface
{
    protected Method $method = Method::GET;
    
    public function resolveEndpoint(): string
    {
        return '/dynamic';
    }
    
    public function createDtoFromResponse(Response $response): Collection
    {
        return new Collection([
            makeProxy(),
            makeProxy()
        ]);
    }
}

test('ProxyConnector 可以处理账号密码代理请求', function () {
    $connector = Mockery::mock(TestProxyConnector::class)->makePartial();
    $request = new TestAccountPasswordRequest();
    $response = Mockery::mock(Response::class);
    
    $connector->shouldReceive('send')
        ->once()
        ->with($request)
        ->andReturn($response);
        
    $proxy = $connector->accountPassword($request);
    
    expect($proxy)->toBeInstanceOf(Proxy::class);
    expect($proxy->getUsername())->toBe('test_user');
    expect($proxy->getPassword())->toBe('test_pass');
});

test('ProxyConnector 可以处理动态代理请求', function () {
    $connector = Mockery::mock(TestProxyConnector::class)->makePartial();
    $request = new TestDynamicRequest();
    $response = Mockery::mock(Response::class);
    
    $connector->shouldReceive('send')
        ->once()
        ->with($request)
        ->andReturn($response);
        
    $collection = $connector->dynamic($request);
    
    expect($collection)->toBeInstanceOf(Collection::class);
    expect($collection)->toHaveCount(2);
    expect($collection[0])->toBeInstanceOf(Proxy::class);
});

test('非 AccountPasswordInterface 请求会抛出异常', function () {
    $connector = new TestProxyConnector();
    $request = Mockery::mock(Request::class);
    
    expect(fn() => $connector->accountPassword($request))
        ->toThrow(\InvalidArgumentException::class, 'Request must implement AccountPasswordInterface');
});

test('非 DynamicInterface 请求会抛出异常', function () {
    $connector = new TestProxyConnector();
    $request = Mockery::mock(Request::class);
    
    expect(fn() => $connector->dynamic($request))
        ->toThrow(\InvalidArgumentException::class, 'Request must implement DynamicInterface');
});