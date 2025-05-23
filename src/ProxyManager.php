<?php

namespace Weijiajia\HttpProxyManager;

use Illuminate\Support\Manager;
use Weijiajia\HttpProxyManager\Drivers\HuaSheng\HuaShengConnector;
use Weijiajia\HttpProxyManager\Drivers\IpRoyal\IpRoyalConnector;
use Weijiajia\HttpProxyManager\Drivers\Smartdaili\SmartdailiConnector;
use Weijiajia\HttpProxyManager\Drivers\SmartProxy\SmartProxyConnector;
use Weijiajia\HttpProxyManager\Drivers\StormProxies\StormProxiesConnector;
use Weijiajia\HttpProxyManager\Drivers\Wandou\WandouConnector;

class ProxyManager extends Manager
{
    /**
     * 获取默认驱动名称.
     */
    public function getDefaultDriver(): ?string
    {
        return $this->config->get('http-proxy-manager.default');
    }

    public function driver($driver = null): ProxyConnector
    {
        return parent::driver($driver);
    }

    /**
     * 创建代理服务实例.
     */
    public function connector(?string $driver = null): ProxyConnector
    {
        return $this->driver($driver);
    }

    /**
     * 创建驱动实例的通用方法.
     *
     * @param string $connectorClass 连接器类名
     * @param string $configKey      配置键名
     */
    protected function createConnector(string $connectorClass, string $configKey): ProxyConnector
    {
        $config = $this->config->get("http-proxy-manager.drivers.{$configKey}", []);

        return $this->container->make($connectorClass, ['config' => $config]);
    }

    /**
     * 创建华盛代理驱动.
     */
    protected function createHuashengdailiDriver(): ProxyConnector
    {
        return $this->createConnector(HuaShengConnector::class, 'huashengdaili');
    }

    /**
     * 创建Storm代理驱动.
     */
    protected function createStormproxiesDriver(): ProxyConnector
    {
        return $this->createConnector(StormProxiesConnector::class, 'stormproxies');
    }

    /**
     * 创建豌豆代理驱动.
     */
    protected function createWandouDriver(): ProxyConnector
    {
        return $this->createConnector(WandouConnector::class, 'wandou');
    }

    /**
     * 创建IpRoyal代理驱动.
     */
    protected function createIproyalDriver(): ProxyConnector
    {
        return $this->createConnector(IpRoyalConnector::class, 'iproyal');
    }

    /**
     * 创建SmartDaili代理驱动.
     */
    protected function createSmartdailiDriver(): ProxyConnector
    {
        return $this->createConnector(SmartdailiConnector::class, 'smartdaili');
    }

    /**
     * 创建SmartProxy代理驱动.
     */
    protected function createSmartproxyDriver(): ProxyConnector
    {
        return $this->createConnector(SmartProxyConnector::class, 'smartproxy');
    }
}
