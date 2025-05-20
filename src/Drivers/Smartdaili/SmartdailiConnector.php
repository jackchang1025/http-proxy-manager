<?php

namespace Weijiajia\HttpProxyManager\Drivers\Smartdaili;

use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\Smartdaili\Request\DirectConnectionIp;
use Weijiajia\HttpProxyManager\ProxyConnector;

class SmartdailiConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://www.smartdaili.com/';
    }

    public function withLifetime(int|string $lifetime): ProxyConnector
    {
        $this->config->add('sessionduration', $lifetime);

        return $this;
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
