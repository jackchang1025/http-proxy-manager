<?php

namespace Weijiajia\HttpProxyManager\Proxys\Smartdaili\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class AccountPassword extends Data
{
    public function __construct(
        public string $username,
        public string $password,
        public string $endpoint,
        public int $port,
        public string $protocol,
    ) {

        if (!in_array($protocol, ['http', 'https', 'socks5'])) {
            throw new \InvalidArgumentException('不支持的协议类型: '.$protocol);
        }
    }
    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'endpoint' => $this->endpoint,
            'port' => $this->port,
            'protocol' => $this->protocol,
        ];
    }

}
