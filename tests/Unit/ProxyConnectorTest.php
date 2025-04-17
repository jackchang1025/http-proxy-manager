<?php

namespace Tests\Unit;


use Weijiajia\HttpProxyManager\ProxyConnector;
use Weijiajia\HttpProxyManager\Request;
use Weijiajia\HttpProxyManager\DirectConnectionIpRequest;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Exception\ProxyModelNotFoundException;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\ProxyFormat;
use Illuminate\Support\Collection;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;



class ProxyConnectorTestExtractIpRequest extends Request
{
    protected Method $method = Method::GET;
    
    public function __construct(
        array $options = [
            'api_key' => 'test_key',
            'count' => 1,
            'country' => 'cn'
        ]
    ) {
        parent::__construct($options);
    }
    
    public function resolveEndpoint(): string
    {
        return '/api/proxy/extract';
    }
    
    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json();
        
        return collect($data['list'])->map(function ($item) {
            return new Proxy(
                host: $item['ip'] ?? '',
                port: $item['port'] ?? 0,
                username: $item['username'] ?? '',
                password: $item['password'] ?? ''
            );
        });
    }
}

// 异常抛出测试类 - 抛出代理异常的请求
class ExceptionThrowingRequest extends Request
{
    protected Method $method = Method::GET;
    
    public function resolveEndpoint(): string
    {
        return '/api/error';
    }
    
    public function createDtoFromResponse(Response $response): Collection
    {
        throw new ProxyException('API返回错误');
    }
}

// 测试用的请求类 - 直连IP
class ProxyConnectorTestDirectConnectionIpRequest extends DirectConnectionIpRequest
{
    protected ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT;
    
    public function __construct(
        array $options = [
            'host' => 'proxy.example.com',
            'port' => 8080,
            'username' => 'testuser',
            'password' => 'testpass',
            'protocol' => 'http',
        ],
        
    ) {
        parent::__construct($options);
    }
    
    
}

// 测试用的连接器 - 支持所有功能
class FullSupportConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.example.com';
    }
    
    protected function getExtractIpRequestClass(): ?string
    {
        return ProxyConnectorTestExtractIpRequest::class;
    }
    
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return ProxyConnectorTestDirectConnectionIpRequest::class;
    }
}

// 测试异常抛出的连接器
class ErrorConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.example.com';
    }
    
    protected function getExtractIpRequestClass(): ?string
    {
        return ExceptionThrowingRequest::class;
    }
    
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return null;
    }
}

// 测试用的连接器 - 只支持直连IP
class OnlyDirectConnectionConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.example.com';
    }
    
    protected function getExtractIpRequestClass(): ?string
    {
        return null;
    }
    
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return ProxyConnectorTestDirectConnectionIpRequest::class;
    }
}

// 测试用的连接器 - 只支持提取IP
class OnlyExtractIpConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.example.com';
    }
    
    protected function getExtractIpRequestClass(): ?string
    {
        return ProxyConnectorTestExtractIpRequest::class;
    }
    
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return null;
    }
}

// 测试用的连接器 - 不支持任何功能
class NoSupportConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.example.com';
    }
    
    protected function getExtractIpRequestClass(): ?string
    {
        return null;
    }
    
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return null;
    }
}

// 测试 extractIp 方法成功提取IP
it('successfully extracts IPs', function () {
    // 由于无法使用 fake 方法进行模拟，我们使用真实调用
    // 创建连接器
    $connector = new FullSupportConnector();

 
    $mockClient = new MockClient([
        '*' => MockResponse::make(body: ['list'=>[
            [
                'sever' => '192.168.1.1',
                'port' => 8080, 
                'user' => 'testuser',
                'password' => 'testpass',
            ]
        ]], status: 200),
    ]);
    
    $connector->withMockClient($mockClient);
    
    // 调用extractIp方法
    $proxies = $connector->extractIp();
    
    // 验证结果是一个集合
    expect($proxies)->toBeInstanceOf(Collection::class);
});

// 测试 directConnectionIp 方法成功获取直连IP
it('successfully gets direct connection IP', function () {
    // 创建连接器
    $connector = new FullSupportConnector();
    
    // 调用directConnectionIp方法
    $proxy = $connector->directConnectionIp([
        'host' => 'direct.example.com',
        'port' => 9090,
        'username' => 'directuser',
        'password' => 'directpass',
        'protocol' => 'http',
    ]);
    
    // 验证结果
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('direct.example.com')
        ->and($proxy->getPort())->toBe(9090)
        ->and($proxy->getUsername())->toBe('directuser')
        ->and($proxy->getPassword())->toBe('directpass')
        ->and($proxy->getProtocol())->toBe('http');
});

// 测试不支持提取IP的连接器
it('throws exception when extract IP is not supported', function () {
    $connector = new OnlyDirectConnectionConnector();
    
    $connector->extractIp();
})->throws(ProxyModelNotFoundException::class);

// 测试不支持直连IP的连接器
it('throws exception when direct connection IP is not supported', function () {
    $connector = new OnlyExtractIpConnector();
    
    $connector->directConnectionIp();
})->throws(ProxyModelNotFoundException::class);

// 测试API异常处理
it('handles proxy exceptions properly', function () {
    $connector = new ErrorConnector();
    
    $mockClient = new MockClient([
        '*' => MockResponse::make(body: '', status: 400),
    ]);
    
    $connector->withMockClient($mockClient);

    $connector->extractIp();
})->throws(\Saloon\Exceptions\Request\RequestException::class);

// 测试参数合并 - 直连IP
it('correctly merges config params for direct connection IP', function () {
    // 设置初始配置
    $config = [
        'host' => 'default.example.com',
            'port' => 1234,
            'username' => 'default_user',
            'password' => 'default_pass',
            'protocol' => 'http',
    ];
    
    // 创建连接器
    $connector = new FullSupportConnector($config);
    
    // 调用directConnectionIp方法并传递覆盖参数
    $proxy = $connector->directConnectionIp([
        'host' => 'override.example.com'
    ]);
    
    // 验证是否正确合并了参数
    expect($proxy->getHost())->toBe('override.example.com')
        ->and($proxy->getPort())->toBe(1234)
        ->and($proxy->getUsername())->toBe('default_user')
        ->and($proxy->getPassword())->toBe('default_pass')
        ->and($proxy->getProtocol())->toBe('http');
}); 