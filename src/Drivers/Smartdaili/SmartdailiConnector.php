<?php

namespace Weijiajia\HttpProxyManager\Drivers\Smartdaili;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\Smartdaili\Request\DirectConnectionIp;

class SmartdailiConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * The Base URL of the API
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.smartdaili.com/';
    }


    protected function getExtractIpRequestClass(): ?string
    {
        return null;
    }

    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return DirectConnectionIp::class;
    }
    
}
