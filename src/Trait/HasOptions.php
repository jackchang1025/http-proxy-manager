<?php

namespace Weijiajia\HttpProxyManager\Trait;

trait HasOptions
{
    public function withHost(string $host): self
    {
        $this->config->add('host', $host);
        return $this;
    }

    public function withPort(int $port): self
    {
        $this->config->add('port', $port);
        return $this;
    }

    public function withProtocol(string $protocol): self
    {
        $this->config->add('protocol', $protocol);
        return $this;
    }

    public function withUsername(string $username): self
    {
        $this->config->add('username', $username);
        return $this;
    }

    public function withPassword(string $password): self
    {
        $this->config->add('password', $password);
        return $this;
    }

    public function withSession(string $session): self
    {
        $this->config->add('session', $session);
        return $this;
    }

    public function withLifetime(int|string $lifetime): self
    {
        $this->config->add('lifetime', $lifetime);
        return $this;
    }

    public function withStickySession(bool $stickySession): self
    {
        $this->config->add('sticky_session', $stickySession);
        return $this;
    }

    public function withCity(string $city): self
    {
        $this->config->add('city', $city);
        return $this;
    }

    public function withState(string $state): self
    {
        $this->config->add('state', $state);
        return $this;
    }

    public function withCountry(string $country): self
    {
        $this->config->add('country', $country);
        return $this;
    }

    public function withOption(string $key, mixed $value): self
    {
        $this->config->add($key, $value);
        return $this;
    }

    public function withIp(string $ip): self
    {
        $this->config->add('ip', $ip);
        return $this;
    }
    
    
}