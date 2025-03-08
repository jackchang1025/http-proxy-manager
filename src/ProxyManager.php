<?php

namespace Weijiajia\HttpProxyManager;

use Illuminate\Support\Manager;
use Weijiajia\HttpProxyManager\Exception\ProxyConfigurationNotFoundException;
use Weijiajia\HttpProxyManager\Exception\ProxyModelNotFoundException;
use InvalidArgumentException;

class ProxyManager extends Manager
{
    /**
     * 配置缓存
     *
     * @var array|null
     */
    protected $proxyConfiguration = null;

    /**
     * 获取默认驱动名称
     *
     * @return string
     */
    public function getDefaultDriver(): ?string
    {
        return $this->config->get('http-proxy-manager.default');
    }

    /**
     * 创建指定驱动的实例
     *
     * @param string|null $driver
     * @return ProxyConnector
     * @throws ProxyConfigurationNotFoundException
     * @throws ProxyModelNotFoundException
     */
    protected function createDriver($driver = null): ProxyConnector
    {
        // 获取所有代理提供商配置
        $providersConfig = $this->config->get('http-proxy-manager.providers');
        if (!$providersConfig) {
            throw new InvalidArgumentException("Proxy providers configuration not found");
        }

        // 确定当前使用的驱动
        $driver = $driver ?: $this->getDefaultDriver();
        if (empty($driver)) {
            throw new InvalidArgumentException("Default driver is not specified");
        }

        // 检查驱动配置是否存在
        if (!isset($providersConfig[$driver])) {
            throw new InvalidArgumentException("Driver {$driver} not configured");
        }

        // 获取驱动配置
        $driverConfig = $providersConfig[$driver];
        
        // 从配置中获取默认模式
        $mode = $driverConfig['default_mode'];
        
        if (empty($mode) || !isset($driverConfig['mode'][$mode])) {
            throw new ProxyModelNotFoundException("Mode not configured for driver {$driver}");
        }

        $modeConfig = $driverConfig['mode'][$mode];
        
        // 合并默认配置和驱动配置
        $mergedConfig = $modeConfig['default_config'] ?? [];


        // 创建组件实例
        $data = $this->createComponent($modeConfig['dto'], $mergedConfig);
        $request = $this->createComponent($modeConfig['request'], ['data' => $data]);
        $connector = $this->createComponent($driverConfig['connector'],['request' => $request]);

        return $connector;
    }


    /**
     * 创建组件实例
     *
     * @param string $class
     * @param mixed ...$parameters
     * @return mixed
     */
    protected function createComponent(string $class, array $parameters = [])
    {
        return $this->container->make($class,$parameters);
    }

    /**
     * 清除所有已解析的驱动实例
     *
     * @return $this
     */
    public function forgetDrivers(): self
    {
        parent::forgetDrivers();
        $this->proxyConfiguration = null;

        return $this;
    }

    /**
     * 创建代理服务实例
     *
     * @param string|null $driver
     * @return ProxyConnector
     */
    public function connector(?string $driver = null): ProxyConnector
    {
        return $this->driver($driver);
    }
}
