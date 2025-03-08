<?php

namespace Weijiajia\HttpProxyManager\Proxys\Stormproxies\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class Dynamic extends Data
{
    public function __construct(
        public string $app_key,
        public string $app_secret,
    ) {
    }

    public function toArray(): array
    {
        return [
            'app_key' => $this->app_key,
            'app_secret' => $this->app_secret,
        ];
    }
}
