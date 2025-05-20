<?php

use Orchestra\Testbench\TestCase;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\ProxyManagerServiceProvider;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "uses()" function to bind a different classes or traits.
|
*/

// 使用Orchestra TestCase作为基础测试类
uses(TestCase::class)->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * 创建用于测试的代理实例.
 */
// 设置测试辅助函数
function makeProxy(array $attributes = []): Proxy
{
    $defaults = [
        'host' => '192.168.1.1',
        'port' => 8080,
        'protocol' => 'http',
        'username' => null,
        'password' => null,
        'url' => null,
        'expiresAt' => null,
        'metadata' => [],
    ];

    $data = array_merge($defaults, $attributes);

    return new Proxy(
        $data['host'],
        $data['port'],
        $data['protocol'],
        $data['username'],
        $data['password'],
        $data['url'],
        $data['expiresAt'],
        $data['metadata']
    );
}

/**
 * 获取测试的包服务提供者.
 *
 * @param mixed $app
 */
function getPackageProviders($app)
{
    return [
        ProxyManagerServiceProvider::class,
    ];
}

/**
 * 定义环境变量.
 *
 * @param mixed $app
 */
function defineEnvironment($app)
{
    // 设置配置
    $app['config']->set('http-proxy-manager.default', 'huashengdaili');

    // 设置华盛代理配置
    $app['config']->set('http-proxy-manager.drivers.huashengdaili', [
        'mode' => 'extract_ip',
        'extract_ip' => [
            'session' => 'test-session',
            'time' => 5,
            'count' => 1,
            'type' => 'json',
        ],
    ]);

    // 设置IpRoyal配置
    $app['config']->set('http-proxy-manager.drivers.iproyal', [
        'mode' => 'direct_connection_ip',
        'direct_connection_ip' => [
            'username' => 'test-user',
            'password' => 'test-pass',
            'host' => 'geo.iproyal.com',
            'port' => 12321,
            'protocol' => 'http',
        ],
    ]);
}
