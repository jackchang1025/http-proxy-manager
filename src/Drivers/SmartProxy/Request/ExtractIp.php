<?php

namespace Weijiajia\HttpProxyManager\Drivers\SmartProxy\Request;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Request;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Data\Proxy;

class ExtractIp extends Request
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

   
    public function __construct(
        public array $options = [],
    ) {

        if(empty($this->options['app_key'])){
            throw new \InvalidArgumentException('app_key 不能为空');
        }

        if(empty($this->options['pt'])){
            throw new \InvalidArgumentException('套餐id 不能为空');
        }

        if(empty($this->options['num'])){
            throw new \InvalidArgumentException('num 不能为空');
        }


        if(!in_array($this->options['protocol'] ?? '', ['http', 'socks5'])) {
            throw new \InvalidArgumentException("不支持的代理协议: {$this->options['protocol']}");
        }

        if(!in_array($this->options['format'] ?? '', ['txt', 'json'])) {
            throw new \InvalidArgumentException("不支持的返回数据格式: {$this->options['format']}");
        }

        if(!in_array($this->options['lb'] ?? '', ['1', '2', '3', '4'])) {
            throw new \InvalidArgumentException("不支持的返回数据格式: {$this->options['lb']}");
        }
    }

    /**
     * @param Response $response
     * @return <Proxy>Collection
     * @throws \JsonException|ProxyException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json();

        if (empty($data['data']['list'])){
            throw new ProxyException(message:$response->body());
        }

       return (new Collection($data['data']['list']))->map(function (string $item) {
            [$host, $port] = explode(':', $item);
            return new Proxy(
                host: $host,
                port: (int)$port,
                protocol: $this->options['protocol'],
                url: $item
            );
        });
    }

    protected function defaultQuery(): array
    {
        return array_filter($this->options, fn($value) => $value !== null);
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/web_v1/ip/get-ip-v3';
    }
}
