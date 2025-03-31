<?php

namespace Weijiajia\HttpProxyManager\Drivers\Wandou\Request;

use Carbon\Carbon;
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Request;
use Weijiajia\HttpProxyManager\Exception\ProxyException;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;

class ExtractIp extends Request
{
    protected Method $method = Method::GET;

    public function __construct(public array $options = []) {
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
                protocol: $this->options['protocol'] ?? 'http',
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
        return array_filter($this->options, fn($value) => $value !== null);
    }
}
