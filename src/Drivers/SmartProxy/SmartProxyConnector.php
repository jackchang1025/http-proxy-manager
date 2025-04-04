<?php

namespace Weijiajia\HttpProxyManager\Drivers\SmartProxy;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\SmartProxy\Request\ExtractIp;
use Weijiajia\HttpProxyManager\Drivers\SmartProxy\Request\DirectConnectionIp;

class SmartProxyConnector extends ProxyConnector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.smartproxy.cn';
    }

    protected function getExtractIpRequestClass(): ?string
    {
        return ExtractIp::class;
    }

    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return DirectConnectionIp::class;
    }

    public function withCountry(string $country): self
    {
        $this->config->add('area', strtoupper($country));
        return $this;
    }

    public function withLifetime(int|string $lifetime): ProxyConnector
    {
        $this->config->add('life', $lifetime);
        return $this;
    }

}
