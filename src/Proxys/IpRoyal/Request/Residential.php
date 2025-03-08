<?php

namespace Weijiajia\HttpProxyManager\Proxys\IpRoyal\Request;

use Weijiajia\HttpProxyManager\Proxys\IpRoyal\Data\Residential as ResidentialData;
use Saloon\Enums\Method;
use Saloon\Http\PendingRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Weijiajia\HttpProxyManager\ProxyRequest;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;

class Residential extends ProxyRequest implements AccountPasswordInterface
{
    protected Method $method = Method::GET;

    public function __construct(readonly public ResidentialData $data)
    {
    }

    public function resolveEndpoint(): string
    {
        return 'residential';
    }
    
    
    public function boot(PendingRequest $pendingRequest): void
    {

        // 构建代理响应
        $mockResponse = $this->buildProxyResponse();


        $mockClient = new MockClient([
            __CLASS__ => MockResponse::make(body: $mockResponse),
        ]);

        $pendingRequest->withMockClient($mockClient);
    }

    protected function buildProxyResponse(): array
    {
        $protocol = $this->data->protocol;
        $port     = $this->data->port;
        $password = $this->buildPassword();

        return [
            'username' => $this->data->username,
            'password' => $password,
            'host'     => $this->data->endpoint,
            'port'     => $port,
            'url'      => $this->buildProxyUrl($this->data->username, $password, $this->data->endpoint, $port, $protocol),
        ];
    }

    protected function buildProxyUrl(
        string $username,
        string $password,
        string $host,
        int $port,
        string $protocol
    ): string {

        $protocol = strtolower($protocol);

        if (!in_array($protocol, ['http', 'socks5'])) {
            throw new \InvalidArgumentException("不支持的协议类型: {$protocol}");
        }

        // 构建认证部分
        $auth = sprintf('%s:%s', $username, $password);

        // 构建主机部分
        $hostPart = sprintf('%s:%d', $host, $port);

        // 组合完整的代理URL
        return sprintf('%s://%s@%s', $protocol, $auth, $hostPart);
    }

    protected function buildPassword(): string
    {
        $extraParams = [
            'country'       => $this->data->country,
            'state'         => $this->data->state,
            'region'        => $this->data->region,
            'session'       => $this->data->session,
            'lifetime'      => $this->data->lifetime,
            'streaming'     => $this->data->streaming,
            'skipispstatic' => $this->data->skipispstatic,
            'skipipslist'   => $this->data->skipipslist,
        ];

        // 过滤掉空值
        $params = array_filter($extraParams, fn($value) => !is_null($value));

        // 如果没有额外参数，直接返回原密码
        if (empty($params)) {
            return $this->data->password;
        }

        // 构建参数字符串
        $paramStrings = [];
        foreach ($params as $key => $value) {
            $paramStrings[] = sprintf('%s-%s', $key, $value);
        }

        // 使用-连接所有参数，并添加到密码后面
        return $this->data->password.'_'.implode('_', $paramStrings);
    }

    protected function defaultQuery(): array
    {
        return $this->data->toArrayFilterNull();
    }

    public function createDtoFromResponse(Response $response): Proxy
    {
        $data = $response->json();

        return Proxy::from([
            'host'     => $data['host'] ?? null,
            'port'     => $data['port'] ?? null,
            'username'     => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'url'      => $data['url'] ?? null,
       ]);

    }
    
}