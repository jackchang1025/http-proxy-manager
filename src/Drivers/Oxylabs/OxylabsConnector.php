<?php

namespace Weijiajia\HttpProxyManager\Drivers\Oxylabs;

use Weijiajia\HttpProxyManager\Drivers\Oxylabs\Request\DirectConnectionIp;
use Weijiajia\HttpProxyManager\ProxyConnector;

class OxylabsConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return '';
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
