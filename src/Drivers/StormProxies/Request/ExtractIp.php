<?php

namespace Weijiajia\HttpProxyManager\Drivers\StormProxies\Request;

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

    public function __construct(public array $options = []) {
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
                protocol: $this->options['protocol'] ?? 'http',
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
        return '/web_v1/ip/get-ip';
    }
}
