<?php

namespace Weijiajia\HttpProxyManager\Proxys\Stormproxies;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;

class StormConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.stormproxies.cn';
    }
}
