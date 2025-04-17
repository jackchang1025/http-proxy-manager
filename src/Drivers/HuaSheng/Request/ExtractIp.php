<?php

namespace Weijiajia\HttpProxyManager\Drivers\HuaSheng\Request;

use Carbon\Carbon;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Weijiajia\HttpProxyManager\Request;

class ExtractIp extends Request
{

    protected Method $method = Method::GET;

    public function __construct(public array $options = [])
    {
    }

    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '/servers.php';
    }

    protected function defaultQuery(): array
    {
        return  array_filter($this->options,fn($value) => $value !== null);
    }

    /**
     * @param Response $response
     * @return Collection
     * @throws ProxyException
     * @throws \JsonException
     */
    public function createDtoFromResponse(Response $response): Collection
    {
        $data = $response->json();

        if (!isset($data['status']) || $data['status'] !== '0' || empty($data['list'])) {
            throw new ProxyException(message: $response->body());
        }

        return (new Collection($data['list']))->map(function (array $item) {
            return Proxy::from([
                'host' => $item['sever'] ?? null,
                'port' => $item['port'] ?? null,
                'user' => $item['user'] ?? null,
                'password' => $item['password'] ?? null,
                'url' => "http://{$item['sever']}:{$item['port']}",
                'expireTime' => isset($item['expire_time']) ? Carbon::parse($item['expire_time']) : null,
            ]);
        });        
    }
}
