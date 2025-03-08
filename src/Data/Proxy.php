<?php

namespace Weijiajia\HttpProxyManager\Data;

use Weijiajia\HttpProxyManager\Contracts\ProxyInterface;
use DateTimeInterface;

class Proxy extends Data implements ProxyInterface
{

    public function __construct(
        protected string $host,
        protected int $port,
        protected string $type = 'http',
        protected ?string $username = null,
        protected ?string $password = null,
        protected ?string $url = null,
        protected ?DateTimeInterface $expiresAt = null,
        protected array $metadata = []
    ) {
    }

    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'type' => $this->type,
            'username' => $this->username,
            'password' => $this->password,
            'expiresAt' => $this->expiresAt,
            'metadata' => $this->metadata,
        ];
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getExpiresAt(): ?DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isValid(): bool
    {
        if ($this->expiresAt === null) {
            return true;
        }

        return $this->expiresAt > new \DateTime();
    }

}