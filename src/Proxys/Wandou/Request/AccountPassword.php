<?php

namespace Weijiajia\HttpProxyManager\Proxys\Wandou\Request;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Proxys\Wandou\Data\AccountPassword as AccountPasswordData;
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
        $username = $this->data->username;

        $data = Arr::only($this->data->toArray(), [
            'life',
            'pid',
            'cid',
            'isp',
        ]);

        if ($city = $this->data->city) {
            $data['cid'] = $city;
        }
        if ($province = $this->data->province) {
            $data['pid'] = $province;
        }

        $data['session'] = time();

//        $data = array_filter($this->dto->all(), static fn($value) => $value !== null);

        foreach ($data as $key => $value) {
            $username .= sprintf("_%s-%s", $key, $value);
        }

        //curl -x db1z2pgm_session-23424_life-2_pid-0_isp-1:o5njazji@gw.wandouapp.com:1000 api.ip.cc
        //curl -x db1z2pgm_session-17299_life-5_isp-0_pid-0:o5njazji@gw.wandouapp.com:1000 api.ip.cc

        // Create a mock client for the flow proxy
        $mockClient = new MockClient([
            __CLASS__ => MockResponse::make(
                body: [
                    'username' => $username,
                    'password' => $this->data->password,
                    'host'     => $this->data->host,
                    'port'     => $this->data->port,
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
     * @return Proxy
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

    public function resolveEndpoint(): string
    {
        return 'proxy';
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArrayFilterNull();
    }
}
