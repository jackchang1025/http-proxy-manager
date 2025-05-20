<?php

namespace Weijiajia\HttpProxyManager;

use Illuminate\Support\Collection;
use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Http\Connector;
use Saloon\Repositories\ArrayStore;
use Saloon\Traits\Conditionable;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Exception\ProxyModelNotFoundException;
use Weijiajia\HttpProxyManager\Trait\HasOptions;
use Weijiajia\SaloonphpLogsPlugin\Contracts\HasLoggerInterface;
use Weijiajia\SaloonphpLogsPlugin\HasLogger;

abstract class ProxyConnector extends Connector implements HasLoggerInterface
{
    use HasLogger;
    use HasOptions;
    use Conditionable;

    public ?int $tries = 3;

    protected ArrayStoreContract $config;

    public function __construct(array $config = [])
    {
        $this->config = new ArrayStore($config);
    }

    public function config(): ArrayStoreContract
    {
        return $this->config;
    }

    /**
     * 构建用于提取 IP 的请求对象
     *
     * @param array $config 运行时配置
     *
     * @throws ProxyModelNotFoundException
     * @throws ProxyException
     */
    public function buildExtractIpRequest(array $config = []): Request
    {
        $requestClass = $this->getExtractIpRequestClass();
        if (null === $requestClass) {
            throw new ProxyModelNotFoundException(class_basename($this).' Driver does not support extractIp mode');
        }

        // 不再从 'extract_ip' 键中读取配置，而是直接使用根级别配置
        // 即 $this->config()->all() 包含了由 HasOptions trait 设置的所有配置
        $connectorConfig = $this->config()->all();

        // 合并配置：连接器配置 < 运行时配置（运行时配置优先级更高）
        $mergedConfig = array_merge($connectorConfig, $config);

        $request = new $requestClass($mergedConfig);

        if (!$request instanceof Request) {
            throw new ProxyException('Request class must be an instance of '.Request::class);
        }

        return $request;
    }

    /**
     * 构建用于直连 IP 的请求对象
     *
     * @param array $config 运行时配置
     *
     * @throws ProxyModelNotFoundException
     * @throws ProxyException
     */
    public function buildDirectConnectionIpRequest(array $config = []): Request
    {
        $requestClass = $this->getDirectConnectionIpRequestClass();
        if (null === $requestClass) {
            throw new ProxyModelNotFoundException(class_basename($this).' Driver does not support directConnectionIp mode');
        }

        // 不再从 'direct_connection_ip' 键中读取配置，而是直接使用根级别配置
        $connectorConfig = $this->config()->all();

        // 合并配置：连接器配置 < 运行时配置（运行时配置优先级更高）
        $mergedConfig = array_merge($connectorConfig, $config);

        $request = new $requestClass($mergedConfig);

        if (!$request instanceof Request) {
            throw new ProxyException('Request class must be an instance of '.Request::class);
        }

        return $request;
    }

    public function extractIp(array $config = []): Collection
    {
        // 构建请求
        $request = $this->buildExtractIpRequest($config);

        // 发送请求
        $response = $this->send($request);

        // 处理响应
        return $request->createDtoFromResponse($response);
    }

    public function directConnectionIp(array $config = []): Proxy
    {
        // 构建请求
        $request = $this->buildDirectConnectionIpRequest($config);

        // 发送请求
        $response = $this->send($request);

        // 处理响应
        return $request->createDtoFromResponse($response);
    }

    /**
     * 根据设置的模式使用默认方法获取代理.
     *
     * @param array $config 运行时配置
     *
     * @throws ProxyModelNotFoundException
     */
    public function defaultModelIp(array $config = []): Collection|Proxy
    {
        $mode = $this->config()->get('mode');

        if ('extract_ip' === $mode) {
            return $this->extractIp($config);
        }

        if ('direct_connection_ip' === $mode) {
            return $this->directConnectionIp($config);
        }

        throw new ProxyModelNotFoundException('Invalid mode: '.$mode);
    }

    abstract protected function getExtractIpRequestClass(): ?string;

    abstract protected function getDirectConnectionIpRequestClass(): ?string;
}
