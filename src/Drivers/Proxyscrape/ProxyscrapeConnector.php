<?php

namespace Weijiajia\HttpProxyManager\Drivers\Proxyscrape;

use Weijiajia\HttpProxyManager\Drivers\Proxyscrape\Request\ExtractIp;
use Weijiajia\HttpProxyManager\ProxyConnector;

class ProxyscrapeConnector extends ProxyConnector
{
    public function resolveBaseUrl(): string
    {
        return 'https://api.proxyscrape.com/v4';
    }

    protected function getExtractIpRequestClass(): ?string
    {
        return ExtractIp::class;
    }

    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return null;
    }
}
