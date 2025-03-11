<?php

namespace Weijiajia\HttpProxyManager\Proxys\SmartProxy\Request;

use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Proxys\SmartProxy\Data\Dynamic as DynamicData;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;

class Dynamic extends ProxyRequest implements DynamicInterface
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(readonly public DynamicData $data)
    {
       
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
                type: 'http',
                url: $item
            );
        });
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArrayFilterNull();
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/web_v1/ip/get-ip-v3';
    }
}
