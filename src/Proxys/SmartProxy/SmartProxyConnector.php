<?php

namespace Weijiajia\HttpProxyManager\Proxys\SmartProxy;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;

class SmartProxyConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.smartproxy.cn';
    }
}
