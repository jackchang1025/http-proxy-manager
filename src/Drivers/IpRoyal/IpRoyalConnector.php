<?php

namespace Weijiajia\HttpProxyManager\Drivers\IpRoyal;

use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\IpRoyal\Request\ResidentialDirectConnectionIp;
use Weijiajia\HttpProxyManager\ProxyConnector;

class IpRoyalConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * 获取API基础URL.
     */
    public function resolveBaseUrl(): string
    {
        return 'https://api.iproyal.com/';
    }

    /**
     * 获取提取IP请求类
     * IpRoyal不支持提取IP模式.
     *
     * @return null|string 请求类的完全限定类名
     */
    protected function getExtractIpRequestClass(): ?string
    {
        return null; // 不支持提取IP模式
    }

    /**
     * 获取直连IP请求类.
     */
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return ResidentialDirectConnectionIp::class;
    }
}
