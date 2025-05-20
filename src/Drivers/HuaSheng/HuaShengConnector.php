<?php

namespace Weijiajia\HttpProxyManager\Drivers\HuaSheng;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\HuaSheng\Request\ExtractIp;
use Weijiajia\ProvinceCityChina\Cities;

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

    /**
     * 设置城市
     *
     * @param string $city 城市名称
     * @return self
     */
    public function withCity(string $city): self
    {
         // Attempt to find cities by Pinyin
         $foundCities = Cities::make()->getCitiesByPinyin($city);

         // If not found by Pinyin, try by province code
         if ($foundCities->isEmpty()) {
             $foundCities = Cities::make()->getCitiesByProvinceCode($city);
         }
 
         // If still not found, throw an exception
         if ($foundCities->isEmpty()) {
             // Using InvalidArgumentException for more specific error type
             // Including the input in the error message for better debugging
             throw new \InvalidArgumentException("City '{$city}' not found or did not yield any results.");
         }
 
         $this->config->add('city', $foundCities->first()->getCode());
        return $this;
    }

    public function withCountry(string $country): self
    {
        return $this;
    }
} 