<?php

namespace Weijiajia\HttpProxyManager\Proxys\IpRoyal;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Saloon\Http\Request;
use Illuminate\Support\Collection;

class IpRoyalConnector extends ProxyConnector
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
        return 'https://api.iproyal.com/';
    }

    public function dynamic(?Request $request = null): Collection
    {
        throw new \Exception('Not implemented');
    }
    
}
