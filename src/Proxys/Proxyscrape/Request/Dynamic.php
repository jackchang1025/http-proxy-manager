<?php

namespace Weijiajia\HttpProxyManager\Proxys\Proxyscrape\Request;

use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Proxys\Proxyscrape\Data\Dynamic as DynamicData;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Saloon\Http\Response;
use Illuminate\Support\Collection;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;
use Saloon\Enums\Method;


class Dynamic extends ProxyRequest implements DynamicInterface
{

    protected Method $method = Method::GET;

    public function __construct(
        public DynamicData $data,
    ) {
    }

    public function resolveEndpoint(): string
    {
        return '/free-proxy-list/get?'.http_build_query($this->data->toArray());
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json('proxies');

        return collect($data)->map(function (array $proxy) {
            return new Proxy(host: $proxy['ip'], port: $proxy['port'], type: $proxy['protocol'],url:$proxy['proxy']);
        });
    }
}