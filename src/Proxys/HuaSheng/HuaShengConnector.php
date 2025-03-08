<?php

namespace Weijiajia\HttpProxyManager\Proxys\HuaSheng;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Saloon\Http\Request;

class HuaShengConnector extends ProxyConnector
{
    use AcceptsJson;

    public ?int $tries = 5;

    public function resolveBaseUrl(): string
    {
        return 'https://mobile.huashengdaili.com/';
    }

    public function accountPassword(?Request $request = null): Proxy
    {
        throw new \Exception('Not implemented');
    }
}
