# HTTP Proxy Manager

A Laravel package for managing HTTP proxies from various proxy service providers with a unified API.

[![Latest Version on Packagist](https://img.shields.io/packagist/v/weijiajia/http-proxy-manager.svg)](https://packagist.org/packages/weijiajia/http-proxy-manager)
[![Total Downloads](https://img.shields.io/packagist/dt/weijiajia/http-proxy-manager.svg)](https://packagist.org/packages/weijiajia/http-proxy-manager)

## 简介

HTTP Proxy Manager 是一个用于管理多个代理服务提供商的 Laravel 包。它提供了一个统一的接口来处理不同代理服务商的 API，使您可以轻松切换和管理 HTTP 代理，而无需修改应用程序代码。

该包基于 [Saloon PHP](https://github.com/saloonphp/saloon) 构建，提供了一个流畅且一致的 API 来处理 HTTP 请求。

## 功能特点

- 统一的接口管理多个代理服务提供商
- 支持动态代理和账号密码认证代理
- 可配置的代理提供商设置
- 与 Laravel 框架无缝集成
- 基于驱动的设计，便于扩展

## 支持的代理服务提供商

- 华声代理 (HuaSheng)
- Storm Proxies
- 豌豆代理 (Wandou)
- IP Royal
- Smart 代理 (Smartdaili)
- Oxylabs
- Proxyscrape

## 安装

通过 Composer 安装：

```bash
composer require weijiajia/http-proxy-manager
```

## 配置

### 发布配置文件

首先，发布配置文件：

```bash
php artisan vendor:publish --provider="Weijiajia\HttpProxyManager\ProxyManagerServiceProvider"
```

这将在 `config/http-proxy-manager.php` 创建配置文件。

### 环境变量

在您的 `.env` 文件中设置默认的代理驱动：

```
PROXY_DRIVER=huashengdaili
```

### 配置文件

配置文件 `http-proxy-manager.php` 包含了所有支持的代理服务提供商的设置。您可以根据需要修改这些设置：

```php
return [
    'name'      => 'proxyManager',
    'default'   => env('PROXY_DRIVER', 'huashengdaili'),
    'providers' => [
        'huashengdaili' => [
            'default_mode' => 'dynamic',
            'connector' => \Weijiajia\HttpProxyManager\Drivers\HuaSheng\HuaShengConnector::class,
            'mode'      => [
                'dynamic' => [
                    'request' => \Weijiajia\HttpProxyManager\Drivers\HuaSheng\Request\Dynamic::class,
                    'dto'     => \Weijiajia\HttpProxyManager\Drivers\HuaSheng\Data\Dynamic::class,
                    'default_config' => [
                        // 默认配置项...
                    ],
                ],
            ],
        ],
        // 其他代理服务提供商配置...
    ],
];
```

## 使用方法

### 基本用法

```php
use Weijiajia\HttpProxyManager\ProxyManager;

// 使用默认驱动获取代理
$proxyManager = new ProxyManager(app());
$proxy = $proxyManager->connector()->default();

// 或使用特定驱动
$proxy = $proxyManager->connector('huashengdaili')->default();
```

### 获取动态代理

```php
// 获取动态代理
$proxies = $proxyManager->connector()->dynamic();

// 获取代理信息
foreach ($proxies as $proxy) {
    echo $proxy->getIp();       // 代理 IP
    echo $proxy->getPort();     // 代理端口
    echo $proxy->getProtocol(); // 代理协议
}
```

### 获取账号密码认证代理

```php
// 获取账号密码认证代理
$proxy = $proxyManager->connector()->accountPassword();

echo $proxy->getIp();       // 代理 IP
echo $proxy->getPort();     // 代理端口
echo $proxy->getUsername(); // 用户名
echo $proxy->getPassword(); // 密码
echo $proxy->getProtocol(); // 代理协议
```

## 不使用 Laravel 框架的用法

本包也可以在非 Laravel 项目中使用。以下示例展示了如何直接使用连接器发送请求。

### 使用花生代理连接器

```php
use Weijiajia\HttpProxyManager\Drivers\HuaSheng\HuaShengConnector;
use Weijiajia\HttpProxyManager\Drivers\HuaSheng\Request\Dynamic;
use Weijiajia\HttpProxyManager\Drivers\HuaSheng\Data\Dynamic as DynamicData;

// 创建数据对象
$data = new DynamicData([
    'time'      => 10,       // IP 有效时长（分钟）
    'count'     => 1,        // 提取的 IP 数量
    'protocol'  => 'HTTP',   // IP 协议
    'type'      => 'json'    // 返回类型
]);

// 创建请求对象
$request = new Dynamic(['data' => $data]);

// 创建连接器
$connector = new HuaShengConnector($request);

// 发送请求并获取动态代理
$proxies = $connector->dynamic();

// 使用代理
foreach ($proxies as $proxy) {
    echo "代理 IP: " . $proxy->getIp() . "\n";
    echo "代理端口: " . $proxy->getPort() . "\n";
    echo "代理协议: " . $proxy->getProtocol() . "\n";
}
```

### 使用 IP Royal 账号密码认证代理

```php
use Weijiajia\HttpProxyManager\Drivers\IpRoyal\IpRoyalConnector;
use Weijiajia\HttpProxyManager\Drivers\IpRoyal\Request\Residential;
use Weijiajia\HttpProxyManager\Drivers\IpRoyal\Data\Residential as ResidentialData;

// 创建数据对象
$data = new ResidentialData([
    'protocol'         => 'http',
    'sticky_session'   => false,
    'session_duration' => 10,
    'endpoint'         => 'geo.iproyal.com'
]);

// 创建请求对象
$request = new Residential(['data' => $data]);

// 创建连接器
$connector = new IpRoyalConnector($request);

// 发送请求并获取账号密码认证代理
$proxy = $connector->accountPassword();

// 使用代理
echo "代理 IP: " . $proxy->getIp() . "\n";
echo "代理端口: " . $proxy->getPort() . "\n";
echo "用户名: " . $proxy->getUsername() . "\n";
echo "密码: " . $proxy->getPassword() . "\n";
echo "代理 URL: " . $proxy->getUrl() . "\n";
```

### 在 HTTP 客户端中使用代理

```php
use GuzzleHttp\Client;

// 假设我们已经获取了代理信息
$proxy = $connector->default();

// 使用 Guzzle HTTP 客户端发送请求
$client = new Client([
    'proxy' => $proxy->getUrl(),
    'auth' => [
        $proxy->getUsername(),
        $proxy->getPassword()
    ]
]);

// 通过代理发送请求
$response = $client->get('https://api.ipify.org?format=json');
$ipInfo = json_decode($response->getBody(), true);

echo "通过代理显示的 IP: " . $ipInfo['ip'] . "\n";
```

## 代理模式

该包支持两种主要的代理模式：

1. **动态代理** (`dynamic`): 提供动态变化的 IP 地址
2. **账号密码代理** (`flow`): 提供需要用户名和密码认证的代理

## 自定义代理提供商

您可以通过扩展基类来添加自己的代理提供商：

1. 创建 Connector 类扩展 `ProxyConnector`
2. 创建 Request 类扩展 `ProxyRequest` 并实现 `DynamicInterface` 或 `AccountPasswordInterface`
3. 创建 DTO 类扩展 `Proxy` 或 `Proxys`
4. 在配置文件中添加您的新代理提供商

## 贡献

欢迎对此项目做出贡献！请随时提交问题或拉取请求。

## 许可证

本项目使用 MIT 许可证 - 详情请查看 [LICENSE](LICENSE) 文件。
