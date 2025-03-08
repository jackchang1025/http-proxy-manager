<?php

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind a different classes or traits.
|
*/

// pest()->extends(Tests\TestCase::class);

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
 * 创建用于测试的代理实例
 */
function makeProxy(array $overrides = [])
{
    $defaults = [
        'host' => '192.168.1.1',
        'port' => 8080,
        'type' => 'http',
        'username' => null,
        'password' => null,
        'expiresAt' => null
    ];

    $data = array_merge($defaults, $overrides);
    
    return new \Weijiajia\HttpProxyManager\Data\Proxy(
        host: $data['host'],
        port: $data['port'],
        type: $data['type'],
        username: $data['username'],
        password: $data['password'],
        expiresAt: $data['expiresAt']
    );
}
