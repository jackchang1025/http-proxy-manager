<?php

namespace Weijiajia\HttpProxyManager\Proxys\Wandou\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class AccountPassword extends Data 
{
    public function __construct(
        public string $username,
        public string $password,
        public string $host,
        public int $port,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $isp = null,
    ) {
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'city' => $this->city,
            'province' => $this->province,
            'isp' => $this->isp,
        ];
    }
}
