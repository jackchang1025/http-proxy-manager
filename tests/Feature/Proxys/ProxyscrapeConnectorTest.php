<?php

use Weijiajia\HttpProxyManager\Proxys\Proxyscrape\Request\Dynamic;
use Weijiajia\HttpProxyManager\Proxys\Proxyscrape\Data\Dynamic as DynamicData;
use Weijiajia\HttpProxyManager\Proxys\Proxyscrape\ProxyscrapeConnector;
use Illuminate\Support\Collection;

test('ProxyscrapeConnector dynamic', function () {
    

    $connector = new ProxyscrapeConnector();

    $proxies = $connector->dynamic(new Dynamic(new DynamicData()));

    expect($proxies)->toBeInstanceOf(Collection::class)->dump();
});

test('ProxyscrapeConnector accountPassword', function () {
    
    $connector = new ProxyscrapeConnector();

    $connector->debug();
    $proxies = $connector->accountPassword(new Dynamic(new DynamicData()));

})->throws(\Exception::class);
