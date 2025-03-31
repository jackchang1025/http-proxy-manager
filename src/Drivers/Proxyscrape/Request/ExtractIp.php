<?php

namespace Weijiajia\HttpProxyManager\Drivers\Proxyscrape\Request;

use Weijiajia\HttpProxyManager\Request;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Saloon\Http\Response;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;


class ExtractIp extends Request
{

    protected Method $method = Method::GET;

    public function __construct(
       public array $options = []
    ) {}

    public function resolveEndpoint(): string
    {
        return '/free-proxy-list/get?'.http_build_query(array_filter($this->options,fn($value) => $value !== null));
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json('proxies');

        return collect($data)->map(function (array $proxy) {
            return new Proxy(host: $proxy['ip'], port: $proxy['port'], protocol: $proxy['protocol'],url:$proxy['proxy']);
        });
    }
}