<?php

namespace Weijiajia\HttpProxyManager\Proxys\SmartProxy\Request;

use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\Proxys\SmartProxy\Data\AccountPassword as AccountPasswordData;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;

class AccountPassword extends ProxyRequest implements AccountPasswordInterface
{
    /**
     * The HTTP method of the request
     */
    protected Method $method = Method::GET;

    public function __construct(readonly public AccountPasswordData $data)
    {
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        $username = $this->data->username;
        foreach ($this->data->toArrayFilterNull() as $key => $value) {
            $username .= sprintf("_%s-%s", $key, $value);
        }

        // Create a mock client for the flow proxy
        $mockClient = new MockClient([  
            __CLASS__ => MockResponse::make(
                body: [
                    'username' => $username,
                    'password' => $this->data->password,
                    'host'     => $this->data->host,
                    'port'     => 1000,
                    'url'      => sprintf(
                        'http://%s:%s@%s:%d',
                        $username,
                        $this->data->password,
                        $this->data->host,
                        $this->data->port
                    ),
                ]
            ),
        ]);

        $pendingRequest->withMockClient($mockClient);
    }

    /**
     * @param Response $response
     * @return mixed
     * @throws \JsonException
     */
    public function createDtoFromResponse(Response $response): Proxy
    {
        $data = $response->json();

        return Proxy::from([
            'host' => $data['host'] ?? null,
            'port' => $data['port'] ?? null,
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'url' => $data['url'] ?? null,
        ]);
    }


    /**
     * The endpoint for the request
     */
    public function resolveEndpoint(): string
    {
        return '';
    }
}
