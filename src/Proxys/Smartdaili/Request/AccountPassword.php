<?php

namespace Weijiajia\HttpProxyManager\Proxys\Smartdaili\Request;

use Weijiajia\HttpProxyManager\Proxys\Smartdaili\Data\AccountPassword as AccountPasswordData;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;

class AccountPassword extends ProxyRequest implements AccountPasswordInterface
{
    protected Method $method = Method::GET;

    public function __construct(readonly public AccountPasswordData $data)
    {

    }


    public function boot(PendingRequest $pendingRequest): void
    {
        $mockResponse = $this->buildProxyResponse();

        $mockClient = new MockClient([
            __CLASS__ => MockResponse::make(body: $mockResponse),
        ]);

        $pendingRequest->withMockClient($mockClient);
    }

    protected function buildProxyResponse(): array
    {
        return [
            'username' => $this->data->username,
            'password' => $this->data->password,
            'host' => $this->data->endpoint,
            'port' => $this->data->port,
            'url'  => sprintf(
                '%s://%s:%s@%s:%d',
                $this->data->protocol,
                $this->data->username,
                $this->data->password,
                $this->data->endpoint,
                $this->data->port
            ),
        ];
    }


    public function resolveEndpoint(): string
    {
        return 'proxy';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArrayFilterNull();
    }

    public function createDtoFromResponse(Response $response): Proxy
    {
        $data = $response->json();

        return Proxy::from([
            'host' => $data['host'] ?? null,
            'port' => $data['port'] ?? null,
            'user' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'url' => $data['url'] ?? null,
        ]);
    }
}
