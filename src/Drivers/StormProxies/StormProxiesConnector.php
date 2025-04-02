<?php

namespace Weijiajia\HttpProxyManager\Drivers\StormProxies;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\StormProxies\Request\ExtractIp;
use Weijiajia\HttpProxyManager\Drivers\StormProxies\Request\DirectConnectionIp;

class StormProxiesConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * The Base URL of the API.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://proxy.stormip.cn';
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
        $this->config->add('area', $country);
        return $this;
    }

    public function withLifetime(int|string $lifetime): self
    {
        $this->config->add('life', $lifetime);
        return $this;
    }
}
