<?php

namespace Weijiajia\HttpProxyManager;

use Illuminate\Support\Arr;
use Saloon\Enums\Method;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;
use Saloon\Http\PendingRequest;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Data\Proxy;

abstract class DirectConnectionIpRequest extends Request
{
    protected ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT;

    protected Method $method = Method::GET;

    public function __construct(
        array $options = [],
    ) {
        parent::__construct($options);

        if (empty($this->options['host'])) {
            throw new \InvalidArgumentException('host 不能为空');
        }

        if (empty($this->options['port'])) {
            throw new \InvalidArgumentException('port 不能为空');
        }

        if (empty($this->options['username'])) {
            throw new \InvalidArgumentException('username 不能为空');
        }

        if (empty($this->options['password'])) {
            throw new \InvalidArgumentException('password 不能为空');
        }

        if (empty($this->options['protocol'])) {
            throw new \InvalidArgumentException('protocol 不能为空');
        }

        if (!in_array($this->options['protocol'], ['http', 'socks5'])) {
            throw new \InvalidArgumentException("不支持的协议类型: {$this->options['protocol']}");
        }
    }

    public function resolveEndpoint(): string
    {
        return '';
    }

    public function getHost(): string
    {
        return (string) ($this->options['host'] ?? '');
    }

    /**
     * 获取代理端口.
     */
    public function getPort(): int
    {
        return (int) ($this->options['port'] ?? 0);
    }

    /**
     * 获取代理用户名.
     */
    public function getUsername(): string
    {
        return (string) ($this->options['username'] ?? '');
    }

    /**
     * 获取代理密码
     */
    public function getPassword(): string
    {
        return (string) ($this->options['password'] ?? '');
    }

    /**
     * 获取代理协议 (http, https, socks5).
     */
    public function getProtocol(): string
    {
        return (string) ($this->options['protocol'] ?? '');
    }

    /**
     * 获取自定义参数.
     *
     * @return array<string, mixed>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 设置代理配置.
     *
     * @param array<string, mixed> $options
     */
    public function setOptions(array $options): self
    {
        $this->options = $options;

        return $this;
    }

    public function boot(PendingRequest $pendingRequest): void
    {
        // 获取所有必要参数
        $host = $this->getHost();
        $port = $this->getPort();
        $username = $this->getUsername();
        $password = $this->getPassword();
        $protocol = $this->getProtocol();

        // 构建代理URL
        $proxyUrl = $this->proxyFormat->builder($protocol, $host, $port, $username, $password);

        // 创建模拟响应
        $mockClient = new MockClient([
            get_class($this) => MockResponse::make(body: [
                'host' => $host,
                'port' => $port,
                'username' => $username,
                'password' => $password,
                'protocol' => $protocol,
                'url' => $proxyUrl,
            ]),
        ]);

        $pendingRequest->withMockClient($mockClient);
    }

    public function createDtoFromResponse(Response $response): Proxy
    {
        $data = $response->json();

        return Proxy::from([
            'host' => $data['host'] ?? '',
            'port' => $data['port'] ?? 80,
            'username' => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'url' => $data['url'] ?? '',
            'protocol' => $data['protocol'] ?? 'http',
            'expiresAt' => $data['expiresAt'] ?? null,
            'metadata' => $data['metadata'] ?? [],
        ]);
    }

    public function generateSessionId(int $length = 20): string
    {
        return bin2hex(random_bytes(max(1, (int) ceil($length / 2))));
    }

    public function generateString(array $options, array|string $separator = '_'): string
    {
        $options = Arr::except($options, ['username', 'password', 'host', 'port', 'protocol']);

        $options = array_filter($options, fn ($value) => !is_null($value) && false !== $value && '' !== $value);

        if (empty($options)) {
            return '';
        }

        $paramStrings = [];
        foreach ($options as $key => $value) {
            if (is_array($value)) {
                $value = implode(',', $value);
            }

            if (is_bool($value)) {
                $value = (int) $value;
            }

            $paramStrings[] = sprintf('%s-%s', $key, $value);
        }

        return implode($separator, $paramStrings);
    }
}
