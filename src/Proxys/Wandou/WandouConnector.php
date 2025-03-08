<?php

namespace Weijiajia\HttpProxyManager\Proxys\Wandou;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;

class WandouConnector extends ProxyConnector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.wandouapp.com/';
    }
}
