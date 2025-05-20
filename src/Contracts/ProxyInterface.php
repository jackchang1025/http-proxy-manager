<?php

namespace Weijiajia\HttpProxyManager\Contracts;

interface ProxyInterface
{
    /**
     * 获取代理端口.
     */
    public function getPort(): int;

    /**
     * 获取代理类型 (http, https, socks5等).
     */
    public function getProtocol(): string;

    /**
     * 获取代理完整地址 (例如: http://ip:port).
     */
    public function getUrl(): string;

    /**
     * 获取代理的认证信息 (用户名和密码, 如果有).
     */
    public function getUsername(): ?string;

    /**
     * 获取代理的密码
     */
    public function getPassword(): ?string;
}
