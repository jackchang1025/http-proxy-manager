<?php

namespace Weijiajia\HttpProxyManager\Drivers\Wandou;

use Weijiajia\HttpProxyManager\ProxyConnector;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Drivers\Wandou\Request\ExtractIp;
use Weijiajia\HttpProxyManager\Drivers\Wandou\Request\DirectConnectionIp;
use Weijiajia\ProvinceCityChina\Cities;

class WandouConnector extends ProxyConnector
{
    use AcceptsJson;

    public function resolveBaseUrl(): string
    {
        return 'https://api.wandouapp.com/';
    }

    protected function getExtractIpRequestClass(): ?string
    {
        return ExtractIp::class;
    }

    protected function getDirectConnectionIpRequestClass(): ?string
    {
        return DirectConnectionIp::class;
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

       $this->config->add('area_id', $foundCities->first()->getCode());
        return $this;
    }

    public function withCountry(string $country): self
    {
        return $this;
    }
}
