<?php

namespace Tests\Feature;

use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Illuminate\Support\Collection;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Exception\ProxyModelNotFoundException;
use Weijiajia\HttpProxyManager\ProxyConnector;
use Weijiajia\HttpProxyManager\Request;
use Mockery;
use Saloon\Enums\Method;

/**
 * 模拟提取 IP 请求类
 */
class MockExtractIpRequest extends Request
{
    protected Method $method = Method::GET;
    
    /**
     * 解析请求端点
     * 
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '/extract';
    }
    
    /**
     * 从响应创建数据传输对象
     * 
     * @param Response $response
     * @return Collection
     * @throws ProxyException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json();
        if (empty($data['proxies'])) {
            throw new ProxyException('Invalid response for extract IP');
        }
        return collect($data['proxies'])->map(fn ($p) => Proxy::from($p));
    }
    
    /**
     * 设置默认查询参数
     * 
     * @return array
     */
    protected function defaultQuery(): array
    {
        return $this->options;
    }
}

/**
 * 模拟直连 IP 请求类
 */
class MockDirectConnectionIpRequest extends Request
{
    protected Method $method = Method::GET;
    
    /**
     * 解析请求端点
     * 
     * @return string
     */
    public function resolveEndpoint(): string
    {
        return '/direct';
    }
    
    /**
     * 从响应创建数据传输对象
     * 
     * @param Response $response
     * @return Proxy
     * @throws ProxyException
     */
    public function createDtoFromResponse(Response $response): Proxy
    {
        $data = $response->json();
        if (empty($data['proxy'])) {
            throw new ProxyException('Invalid response for direct connection IP');
        }
        return Proxy::from($data['proxy']);
    }
    
    /**
     * 引导请求，配置选项
     * 
     * @param PendingRequest $pendingRequest
     * @return void
     */
    public function boot(PendingRequest $pendingRequest): void
    {
        $pendingRequest->query()->merge($this->options);
    }
}

/**
 * 测试用 ProxyConnector 实现类
 */
class TestProxyConnector extends ProxyConnector
{
    /** @var string|null 提取 IP 请求类 */
    public ?string $extractIpRequestClass = MockExtractIpRequest::class;
    
    /** @var string|null 直连 IP 请求类 */
    public ?string $directConnectionIpRequestClass = MockDirectConnectionIpRequest::class;
    

    /**
     * 解析基础 URL
     * 
     * @return string
     */
    public function resolveBaseUrl(): string
    {
        return 'https://test-proxy.com/api';
    }
    
    /**
     * 获取提取 IP 请求类
     * 
     * @return string|null
     */
    protected function getExtractIpRequestClass(): ?string
    {
        return $this->extractIpRequestClass;
    }
    
    /**
     * 获取直连 IP 请求类
     * 
     * @return string|null
     */
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return $this->directConnectionIpRequestClass;
    }
}

// =============================================================================
// 测试用例
// =============================================================================


// --- 请求构建测试 ---

it('使用合并的配置构建提取 IP 请求', function () {

    $connector = new TestProxyConnector(['common_option' => 'connector_val']);

    $runtimeConfig = ['count' => 5, 'common_option' => 'runtime_val'];
    
    $request = $connector->buildExtractIpRequest($runtimeConfig);
    
    expect($request)->toBeInstanceOf(MockExtractIpRequest::class);
    
    // 验证配置合并
    $expectedOptions = array_merge(
        ['base_extract' => 'value1'], // 从 TestProxyConnector 基础配置获取
        ['common_option' => 'connector_val'], // 从连接器配置获取，但会被运行时配置覆盖
        $runtimeConfig // 运行时配置优先级最高
    );
    
    // 使用显式添加的 getOptions 方法验证
    $actualOptions = $request->getOptions();

    
    // 实际上，运行时配置会覆盖连接器配置中同名的项
    $expectedOptions['common_option'] = 'runtime_val';
    
    expect($actualOptions)->toHaveKey('common_option', 'runtime_val');
    expect($actualOptions)->toHaveKey('count', 5);
});

it('使用合并的配置构建直连 IP 请求', function () {
    $connector = new TestProxyConnector(['common_option' => 'connector_val']);
    $runtimeConfig = ['sticky' => true, 'common_option' => 'runtime_val'];
    
    $request = $connector->buildDirectConnectionIpRequest($runtimeConfig);
    
    expect($request)->toBeInstanceOf(MockDirectConnectionIpRequest::class);
    
    // 获取请求配置，验证配置合并
    $requestOptions = $request->getOptions();
    
    expect($requestOptions)->toHaveKey('common_option', 'runtime_val');
    expect($requestOptions)->toHaveKey('sticky', true);
});

it('当提取 IP 请求类未定义时抛出异常', function () {
    $connector = new TestProxyConnector();
    $connector->extractIpRequestClass = null;
    
    $connector->buildExtractIpRequest();
})->throws(ProxyModelNotFoundException::class, 'TestProxyConnector Driver does not support extractIp mode');

it('当直连 IP 请求类未定义时抛出异常', function () {
    $connector = new TestProxyConnector();
    $connector->directConnectionIpRequestClass = null;
    
    $connector->buildDirectConnectionIpRequest();
})->throws(ProxyModelNotFoundException::class, 'TestProxyConnector Driver does not support directConnectionIp mode');

// --- 代理获取测试 ---

it('成功提取 IP 代理', function () {
    $mockResponseData = [
        'proxies' => [
            ['host' => '1.1.1.1', 'port' => 8080, 'protocol' => 'http'],
            ['host' => '2.2.2.2', 'port' => 9090, 'protocol' => 'socks5'],
        ]
    ];
    
    // 创建 MockClient 实例
    $mockClient = new MockClient([
        MockExtractIpRequest::class => MockResponse::make($mockResponseData, 200),
    ]);
    
    $connector = new TestProxyConnector(['timeout' => 30]);
    $connector->withMockClient($mockClient);
    
    $proxies = $connector->extractIp(['count' => 2]);
    
    expect($proxies)->toBeInstanceOf(Collection::class)
        ->and($proxies->count())->toBe(2)
        ->and($proxies->first())->toBeInstanceOf(Proxy::class)
        ->and($proxies->first()->getHost())->toBe('1.1.1.1')
        ->and($proxies->last()->getProtocol())->toBe('socks5');
    
    $mockClient->assertSent(MockExtractIpRequest::class);
});

it('成功获取直连 IP 代理', function () {
    $mockResponseData = [
        'proxy' => ['host' => 'direct.proxy.com', 'port' => 1234, 'protocol' => 'http', 'username' => 'user', 'password' => 'pass']
    ];
    
    // 创建 MockClient 实例
    $mockClient = new MockClient([
        MockDirectConnectionIpRequest::class => MockResponse::make($mockResponseData, 200),
    ]);
    
    $connector = new TestProxyConnector(['region' => 'us']);
    $connector->withMockClient($mockClient);
    
    $proxy = $connector->directConnectionIp(['sticky' => true]);
    
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('direct.proxy.com')
        ->and($proxy->getUsername())->toBe('user');
    
    $mockClient->assertSent(MockDirectConnectionIpRequest::class);
});

it('使用 extract_ip 模式时 defaultModelIp 返回 Collection', function () {
    // 创建 MockClient 实例
    $mockClient = new MockClient([
        MockExtractIpRequest::class => MockResponse::make(['proxies' => [['host' => '1.1.1.1', 'port' => 8080]]]),
    ]);
    
    $connector = new TestProxyConnector(['mode' => 'extract_ip']);
    $connector->withMockClient($mockClient);
    
    $result = $connector->defaultModelIp();
    
    expect($result)->toBeInstanceOf(Collection::class);
    $mockClient->assertSent(MockExtractIpRequest::class);
});

it('使用 direct_connection_ip 模式时 defaultModelIp 返回 Proxy', function () {
    // 创建 MockClient 实例
    $mockClient = new MockClient([
        MockDirectConnectionIpRequest::class => MockResponse::make(['proxy' => ['host' => 'direct.proxy.com', 'port' => 1234]]),
    ]);
    
    $connector = new TestProxyConnector(['mode' => 'direct_connection_ip']);
    $connector->withMockClient($mockClient);
    
    $result = $connector->defaultModelIp();
    
    expect($result)->toBeInstanceOf(Proxy::class);
    $mockClient->assertSent(MockDirectConnectionIpRequest::class);
});

it('模式无效时 defaultModelIp 抛出异常', function () {
    $connector = new TestProxyConnector(['mode' => 'invalid_mode']);
    $connector->defaultModelIp();
})->throws(ProxyModelNotFoundException::class, 'Invalid mode: invalid_mode');

// --- HasOptions Trait 测试 ---

it('正确设置并传递 HasOptions trait 方法配置', function () {
    $connector = new TestProxyConnector();
    
    // 使用 HasOptions trait 提供的 with* 方法设置配置
    $connector->withHost('my.host.com')
        ->withPort(9999)
        ->withProtocol('socks5')
        ->withUsername('test-user')
        ->withPassword('test-pass')
        ->withSession('abc-123')
        ->withLifetime(60)
        ->withStickySession(true)
        ->withCity('London')
        ->withState('ENG')
        ->withCountry('GB')
        ->withOption('custom_key', 'custom_value')
        ->withIp('192.168.1.100');
    
    // 验证配置已添加到连接器的 config 存储中
    $config = $connector->config()->all();
    
    // 注意：配置会包含构造函数中添加的 extract_ip 和 direct_connection_ip 配置
    expect($config)
        ->toHaveKey('host', 'my.host.com')
        ->toHaveKey('port', 9999)
        ->toHaveKey('protocol', 'socks5')
        ->toHaveKey('username', 'test-user')
        ->toHaveKey('password', 'test-pass')
        ->toHaveKey('session', 'abc-123')
        ->toHaveKey('lifetime', 60)
        ->toHaveKey('sticky_session', true)
        ->toHaveKey('city', 'London')
        ->toHaveKey('state', 'ENG')
        ->toHaveKey('country', 'GB')
        ->toHaveKey('custom_key', 'custom_value')
        ->toHaveKey('ip', '192.168.1.100');
});

it('将 HasOptions 设置的配置传递到请求对象', function () {
    $connector = new TestProxyConnector();
    
    // 使用 HasOptions trait 设置配置
    $connector->withHost('proxy.example.com')
        ->withPort(9876)
        ->withProtocol('socks5')
        ->withUsername('testuser')
        ->withPassword('testpass')
        ->withSession('test-session-123')
        ->withLifetime(30);
    
    // 创建提取 IP 请求
    $extractRequest = $connector->buildExtractIpRequest();
    expect($extractRequest)->toBeInstanceOf(MockExtractIpRequest::class);
    
    // 验证请求对象包含所有通过 with* 方法设置的配置
    $requestOptions = $extractRequest->getOptions();
    expect($requestOptions)
        ->toHaveKey('host', 'proxy.example.com')
        ->toHaveKey('port', 9876)
        ->toHaveKey('protocol', 'socks5')
        ->toHaveKey('username', 'testuser')
        ->toHaveKey('password', 'testpass')
        ->toHaveKey('session', 'test-session-123')
        ->toHaveKey('lifetime', 30);
    
    // 验证直连 IP 请求也能接收配置
    $directRequest = $connector->buildDirectConnectionIpRequest();
    expect($directRequest)->toBeInstanceOf(MockDirectConnectionIpRequest::class);
    
    $directOptions = $directRequest->getOptions();
    expect($directOptions)
        ->toHaveKey('host', 'proxy.example.com')
        ->toHaveKey('port', 9876)
        ->toHaveKey('protocol', 'socks5')
        ->toHaveKey('username', 'testuser')
        ->toHaveKey('password', 'testpass');
    
    // 验证运行时配置优先级更高
    $overrideRequest = $connector->buildExtractIpRequest([
        'host' => 'override.example.com',
        'custom_param' => 'custom_value'
    ]);
    
    $overrideOptions = $overrideRequest->getOptions();
    expect($overrideOptions)
        ->toHaveKey('host', 'override.example.com') // 被运行时配置覆盖
        ->toHaveKey('port', 9876) // 保留原值
        ->toHaveKey('custom_param', 'custom_value'); // 添加新值
});

// --- 清理资源 ---

// 测试后清理 Mockery
uses()->afterEach(function () {
    Mockery::close();
});
