<?php

namespace Weijiajia\HttpProxyManager\Drivers\HuaSheng;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\HuaSheng\Request\ExtractIp;

class HuaShengConnector extends ProxyConnector
{
    use AcceptsJson;

    /**
     * 获取API基础URL
     */
    public function resolveBaseUrl(): string
    {
        return 'https://mobile.huashengdaili.com/';
    }

    /**
     * 获取提取IP请求类
     * 
     * @return string|null 请求类的完全限定类名
     */
    protected function getExtractIpRequestClass(): ?string
    {
        return ExtractIp::class;
    }

    /**
     * 获取直连IP请求类
     * 华盛代理不支持直连IP模式
     * 
     * @return string|null
     */
    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return null; // 不支持直连IP模式
    }
} 