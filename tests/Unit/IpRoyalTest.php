<?php

use Weijiajia\HttpProxyManager\Proxys\IpRoyal\IpRoyalConnector;
use Weijiajia\HttpProxyManager\Proxys\IpRoyal\Request\Residential;
use Weijiajia\HttpProxyManager\Proxys\IpRoyal\Data\ResidentialData;

test('IpRoyalConnector Residential', function () {
    
    // 创建一个代理连接器
    $connector = new IpRoyalConnector();

    $connector->debug();

    // 创建一个代理请求
    $request = new Residential(new ResidentialData(username: 'your_username', password: 'your_password', endpoint: 'gw.wandouapp.com', port: 1000, protocol: 'http'));

    $response = $connector->accountPassword($request);

    dd($response);
    // 执行请求
});