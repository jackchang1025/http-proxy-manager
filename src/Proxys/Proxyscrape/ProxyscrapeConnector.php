<?php

namespace Weijiajia\HttpProxyManager\Proxys\Proxyscrape;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Saloon\Http\Request;


class ProxyscrapeConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.proxyscrape.com/v4';
    }

    public function accountPassword(?Request $request = null): Proxy
    {
       throw new \Exception('Not implemented');
    }

}
