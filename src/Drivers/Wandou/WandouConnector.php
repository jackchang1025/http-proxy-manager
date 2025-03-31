<?php

namespace Weijiajia\HttpProxyManager\Drivers\Wandou;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\Wandou\Request\ExtractIp;
use Weijiajia\HttpProxyManager\Drivers\Wandou\Request\DirectConnectionIp;

class WandouConnector extends ProxyConnector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.wandouapp.com/';
    }

    protected function getExtractIpRequestClass(): ?string
    {
        return ExtractIp::class;
    }

    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return DirectConnectionIp::class;
    }
    
}
