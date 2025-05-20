<?php

namespace Weijiajia\HttpProxyManager;

enum ProxyFormat: string
{
    case HOST_PORT_USER_PASS_COLON = 'HOST:PORT:USER:PASS';

    case HOST_PORT_AT_USER_PASS = 'HOST:PORT@USER:PASS';

    case USER_PASS_HOST_PORT_COLON = 'USER:PASS:HOST:PORT';

    case USER_PASS_AT_HOST_PORT = 'USER:PASS@HOST:PORT';

    public function builder(string $protocol, string $host, int $port, string $username, string $password): string
    {
        return match ($this) {
            self::HOST_PORT_USER_PASS_COLON => sprintf('%s://%s:%d:%s:%s', $protocol, $host, $port, $username, $password),
            self::HOST_PORT_AT_USER_PASS => sprintf('%s://%s:%d@%s:%s', $protocol, $host, $port, $username, $password),
            self::USER_PASS_HOST_PORT_COLON => sprintf('%s://%s:%s:%s:%d', $protocol, $username, $password, $host, $port),
            self::USER_PASS_AT_HOST_PORT => sprintf('%s://%s:%s@%s:%d', $protocol, $username, $password, $host, $port),
            default => throw new \InvalidArgumentException("Not supported proxy format: {$this->value}"),
        };
    }
}
