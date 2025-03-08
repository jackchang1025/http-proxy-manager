<?php

use Weijiajia\HttpProxyManager\Proxys\Wandou\WandouConnector;
use Weijiajia\HttpProxyManager\Proxys\Wandou\Request\DynamicRequest;
use Weijiajia\HttpProxyManager\Proxys\Wandou\Request\AccountPasswordRequest;
use Weijiajia\HttpProxyManager\Proxys\Wandou\Data\Dynamic as DynamicData;
use Weijiajia\HttpProxyManager\Proxys\Wandou\Data\AccountPassword as AccountPasswordData;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Contracts\ProxyInterface;
use Saloon\Enums\Method;
test('WandouConnector DynamicRequest', function () {
    
    // 创建一个代理连接器
    $connector = new WandouConnector();

    $connector->debug();

    // 创建一个代理请求
    $request = new DynamicRequest(new DynamicData(app_key: 'your_app_key', app_secret: 'your_app_secret'));

    $response = $connector->send($request);

    $response->dto();

    // 执行请求
})->throws(ProxyException::class);


test('WandouConnector AccountPasswordRequest', function () {
    

    // 创建一个代理连接器
    $connector = new WandouConnector();

    $connector->debug();

    // 创建一个代理请求
    $request = new AccountPasswordRequest(new AccountPasswordData(username: 'your_username', password: 'your_password', host: 'gw.wandouapp.com', port: 1000));

    $response = $connector->send($request);

    expect($response->dto())->toBeInstanceOf(ProxyInterface::class);

    // 执行请求
});