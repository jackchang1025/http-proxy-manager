<?php

namespace Weijiajia\HttpProxyManager\Proxys\Wandou\Request;

use Carbon\Carbon;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Proxys\Wandou\Data\Dynamic as DynamicData;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;

class Dynamic extends ProxyRequest implements DynamicInterface
{
    protected Method $method = Method::GET;

    public function __construct(public readonly DynamicData $data)
    {
   
    }

    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json();

        if ($data['code'] !== 200) {
            throw new ProxyException(message:$response->body());
        }

        return (new Collection($data['data']))->map(function (array $item) {
            
            return new Proxy(
                host: $item['ip'],
                port: (int)$item['port'],
                type: 'http',
                url: "http://{$item['ip']}:{$item['port']}",
                expiresAt: isset($item['expire_time']) ? Carbon::parse($item['expire_time']) : null,
            );
        });

    }

    public function resolveEndpoint(): string
    {
        return '';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArrayFilterNull();
    }
}
