<?php

namespace Weijiajia\HttpProxyManager\Proxys\Smartdaili;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Http\Request;
use Weijiajia\HttpProxyManager\Data\Proxy;

class SmartdailiConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * Default number of retries for failed requests
     */
    public ?int $tries = 3;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.smartdaili.com/';
    }

    public function accountPassword(?Request $request = null): Proxy
    {
        throw new \Exception('Not implemented');
    }
    
}
