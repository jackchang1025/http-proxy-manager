<?php

namespace Weijiajia\HttpProxyManager;

use Saloon\Http\Connector;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Saloon\Contracts\ArrayStore as ArrayStoreContract;
use Saloon\Repositories\ArrayStore;
use Weijiajia\SaloonphpLogsPlugin\HasLogger;
use Weijiajia\SaloonphpLogsPlugin\Contracts\HasLoggerInterface;
use Weijiajia\HttpProxyManager\Exception\ProxyModelNotFoundException;
use Weijiajia\HttpProxyManager\Exception\ProxyException;

abstract class ProxyConnector extends Connector implements HasLoggerInterface
{
    use HasLogger;
   
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

  
    public function extractIp(array $config = []): Collection
    {
        $requestClass = $this->getExtractIpRequestClass();
        if ($requestClass === null) {
            throw new ProxyModelNotFoundException(class_basename($this) . 'Driver does not support extractIp mode');
        }


        $modeConfig = $this->config->get('extract_ip', []);
        $mergedConfig = array_merge($modeConfig, $config);

        $request = new $requestClass($mergedConfig);

        if(!$request instanceof Request){
            throw new ProxyException('Request must be an instance of ' . Request::class);
        }
        
        $response = $this->send($request);
        return $request->createDtoFromResponse($response);
    }

 
    public function directConnectionIp(array $config = []): Proxy
    {
        $requestClass = $this->getDirectConnectionIpRequestClass();
        if ($requestClass === null) {
            throw new ProxyModelNotFoundException(class_basename($this) . 'Driver does not support directConnectionIp mode');
        }

        $modeConfig = $this->config->get('direct_connection_ip', []);
        $mergedConfig = array_merge($modeConfig, $config);

        $request = new $requestClass($mergedConfig);

        if(!$request instanceof Request){
            throw new ProxyException('Request must be an instance of ' . Request::class);
        }
        

        $response = $this->send($request);

        return $request->createDtoFromResponse($response);
    }

    public function defaultModelIp(array $config = []): Proxy|Collection
    {
        if($this->config->get('mode') === 'extract_ip'){
            return $this->extractIp($config);
        }

        if($this->config->get('mode') === 'direct_connection_ip'){
            return $this->directConnectionIp($config);
        }

        throw new ProxyModelNotFoundException('Invalid mode: ' . $this->config->get('mode'));
    }

    
    abstract protected function getExtractIpRequestClass(): ?string;

   
    abstract protected function getDirectConnectionIpRequestClass(): ?string;
}
