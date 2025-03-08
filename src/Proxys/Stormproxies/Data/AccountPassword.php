<?php

namespace Weijiajia\HttpProxyManager\Proxys\Stormproxies\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class AccountPassword extends Data
{
    public function __construct(
        public string $username,
        public string $password,
        public string $host,
        public int $port = 1000,
    ) {
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
        ];
    }
}
