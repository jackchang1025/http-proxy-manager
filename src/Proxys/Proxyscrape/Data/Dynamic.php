<?php

namespace Weijiajia\HttpProxyManager\Proxys\Proxyscrape\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class Dynamic extends Data
{
    public function __construct(
        public string $request = 'display_proxies',
        public string $country = 'us',
        public string $protocol = 'http',
        public string $proxy_format = 'protocolipport',
        public string $format = 'json',
        public int $timeout = 20000,
    ) {}

    public function toArray(): array
    {
        return [
            'request' => $this->request,
            'country' => $this->country,
            'protocol' => $this->protocol,
            'proxy_format' => $this->proxy_format,
            'format' => $this->format,
            'timeout' => $this->timeout,
        ];
    }
}