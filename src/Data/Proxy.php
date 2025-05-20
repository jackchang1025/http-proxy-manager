<?php

namespace Weijiajia\HttpProxyManager\Data;

use Weijiajia\HttpProxyManager\Contracts\ProxyInterface;

class Proxy implements ProxyInterface
{
    /**
     * Summary of __construct.
     *
     * @param array<string,mixed> $metadata
     */
    public function __construct(
        protected string $host,
        protected int $port,
        protected string $protocol = 'http',
        protected ?string $username = null,
        protected ?string $password = null,
        protected ?string $url = null,
        protected ?\DateTimeInterface $expiresAt = null,
        protected array $metadata = []
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'host' => $this->host,
            'port' => $this->port,
            'protocol' => $this->protocol,
            'username' => $this->username,
            'password' => $this->password,
            'expiresAt' => $this->expiresAt,
            'metadata' => $this->metadata,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function from(array $data): self
    {
        return new self(...$data);
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    public function getProtocol(): string
    {
        return $this->protocol;
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

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }

    public function isValid(): bool
    {
        if (null === $this->expiresAt) {
            return true;
        }

        return $this->expiresAt > new \DateTime();
    }

    /**
     * 设置过期时间.
     */
    public function setExpiresAt(?\DateTimeInterface $expiresAt): self
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * 设置元数据.
     *
     * @param array <string, mixed> $metadata
     */
    public function setMetadata(array $metadata): self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * 获取元数据.
     *
     * @return array <string, mixed>
     */
    public function getMetadata(): array
    {
        return $this->metadata;
    }
}
