<?php

namespace Weijiajia\HttpProxyManager\Contracts;

interface ProxyInterface
{
    /**
     * 获取代理端口
     *
     * @return int
     */
    public function getPort(): int;

    /**
     * 获取代理类型 (http, https, socks5等)
     *
     * @return string
     */
    public function getType(): string;

    /**
     * 获取代理完整地址 (例如: http://ip:port)
     *
     * @return string
     */
    public function getUrl(): string;

    /**
     * 获取代理的认证信息 (用户名和密码, 如果有)
     *
     * @return array|null [username, password] 或 null 如果不需要认证
     */
    public function getUsername(): ?string;

    /**
     * 获取代理的密码
     *
     * @return string|null
     */
    public function getPassword(): ?string;
} 