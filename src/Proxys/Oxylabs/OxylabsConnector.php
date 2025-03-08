<?php

namespace Weijiajia\HttpProxyManager\Proxys\Oxylabs;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Saloon\Http\Request;


class OxylabsConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return '';
    }

    public function dynamic(?Request $request = null): Collection
    {
        throw new \Exception('Not implemented');
    }

}
