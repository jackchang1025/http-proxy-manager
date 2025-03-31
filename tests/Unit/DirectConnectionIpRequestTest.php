<?php

namespace Tests\Unit;

use Weijiajia\HttpProxyManager\DirectConnectionIpRequest;
use Weijiajia\HttpProxyManager\ProxyFormat;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Saloon\Http\Connector;

class TestDirectConnectionIpRequest extends DirectConnectionIpRequest
{
    public function __construct(
        array $options = [
            'host' => 'proxy.example.com',
            'port' => 8080,
            'username' => 'testuser',
            'password' => 'testpass',
            'protocol' => 'http',
        ],
        ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT
    ) {
        parent::__construct($options);
        $this->proxyFormat = $proxyFormat;
    }
}

class TestConnector extends Connector
{
    public function resolveBaseUrl(): string
    {
        return 'https://example.com';
    }
}

it('creates proxy object with http protocol', function () {
    // 创建测试请求对象
    $request = new TestDirectConnectionIpRequest(
        options: [
            'host' => 'proxy.example.com',
            'port' => 8080,
            'username' => 'testuser',
            'password' => 'testpass',
            'protocol' => 'http',
        ]
    );

    // 创建连接器
    $connector = new TestConnector();


    // 发送请求
    $response = $connector->send($request);

    // 测试响应
    expect($response->status())->toBe(200);
    
    $proxy = $request->createDtoFromResponse($response);
    
    // 验证生成的代理对象
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('proxy.example.com')
        ->and($proxy->getPort())->toBe(8080)
        ->and($proxy->getUsername())->toBe('testuser')
        ->and($proxy->getPassword())->toBe('testpass')
        ->and($proxy->getUrl())->toBe('http://testuser:testpass@proxy.example.com:8080');
});

it('creates proxy object with socks5 protocol', function () {
    // 创建测试请求对象
    $request = new TestDirectConnectionIpRequest(
        options: [
            'host' => 'proxy.example.com',
            'port' => 8080,
            'username' => 'testuser',
            'password' => 'testpass',
            'protocol' => 'socks5',
        ]
    );

    // 创建连接器
    $connector = new TestConnector();

    // 发送请求
    $response = $connector->send($request);
    
    $proxy = $request->createDtoFromResponse($response);
    
    // 验证生成的代理对象
    expect($proxy)->toBeInstanceOf(Proxy::class)
        ->and($proxy->getHost())->toBe('proxy.example.com')
        ->and($proxy->getPort())->toBe(8080)
        ->and($proxy->getUrl())->toBe('socks5://testuser:testpass@proxy.example.com:8080');
});

it('throws exception for unsupported protocol', function () {
    // 创建测试请求对象
    $request = new TestDirectConnectionIpRequest(
        options: [
            'host' => 'proxy.example.com',
            'port' => 8080,
            'username' => 'testuser',
            'password' => 'testpass',
            'protocol' => 'ftp',
        ]
    );

    // 创建连接器
    $connector = new TestConnector();

    // 调用boot方法，应该抛出异常
    $response = $connector->send($request);
})->throws(\InvalidArgumentException::class, '不支持的协议类型: ftp');

it('creates proxy object with different proxy format', function () {
    // 使用不同的代理格式
    $request = new TestDirectConnectionIpRequest(
        options: [
            'host' => 'proxy.example.com',
            'port' => 8080,
            'username' => 'testuser',
            'password' => 'testpass',
            'protocol' => 'http',
        ],
        proxyFormat: ProxyFormat::HOST_PORT_USER_PASS_COLON
    );

    // 创建连接器
    $connector = new TestConnector();

    // 发送请求
    $response = $connector->send($request);
    
    $proxy = $request->createDtoFromResponse($response);
    
    // 验证生成的代理对象
    $proxyUrl = $proxy->getUrl();
    expect($proxyUrl)->toBe('http://proxy.example.com:8080:testuser:testpass');
});

it('correctly generates session id', function () {
    $request = new TestDirectConnectionIpRequest();
    
    $sessionId = $request->generateSessionId();
    
    expect($sessionId)->toBeString()
        ->and(strlen($sessionId))->toBe(8);
});



// 添加测试以验证不同格式的代理字符串生成
it('generates different proxy formats correctly', function () {
    // 测试不同的格式
    $formats = [
        ProxyFormat::HOST_PORT_USER_PASS_COLON,
        ProxyFormat::HOST_PORT_AT_USER_PASS,
        ProxyFormat::USER_PASS_HOST_PORT_COLON,
        ProxyFormat::USER_PASS_AT_HOST_PORT,
    ];
    
    $expectedUrls = [
        'http://proxy.example.com:8080:testuser:testpass',
        'http://proxy.example.com:8080@testuser:testpass',
        'http://testuser:testpass:proxy.example.com:8080',
        'http://testuser:testpass@proxy.example.com:8080',
    ];
    
    foreach ($formats as $index => $format) {
        $request = new TestDirectConnectionIpRequest(
            proxyFormat: $format
        );
        
        // 创建连接器
        $connector = new TestConnector();
    
        
        // 发送请求
        $response = $connector->send($request);
        
        $proxy = $request->createDtoFromResponse($response);
        
        // 验证URL格式
        expect($proxy->getUrl())->toBe($expectedUrls[$index]);
    }
}); 