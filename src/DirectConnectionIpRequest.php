<?php

namespace Weijiajia\HttpProxyManager;

use Saloon\Http\PendingRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse; 
use Saloon\Enums\Method;
use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Arr;

abstract class DirectConnectionIpRequest extends Request
{
    protected ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT;

    protected Method $method = Method::GET;


    public function __construct(
        public array $options = [],
    ) {

        if(empty($this->options['host'])){
            throw new \InvalidArgumentException('host 不能为空');
        }

        if(empty($this->options['port'])){
            throw new \InvalidArgumentException('port 不能为空');
        }

        if(empty($this->options['username'])){
            throw new \InvalidArgumentException('username 不能为空');
        }

        if(empty($this->options['password'])){
            throw new \InvalidArgumentException('password 不能为空');
        }

        if(empty($this->options['protocol'])){
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
        return $this->options['host'];
    }
    
    /**
     * 获取代理端口
     */
    public function getPort(): int
    {
        return $this->options['port'];
    }
    
    /**
     * 获取代理用户名
     */
    public function getUsername(): string
    {
        return $this->options['username'];
    }
    
    /**
     * 获取代理密码
     */
    public function getPassword(): string
    {
        return $this->options['password'];
    }
    
    /**
     * 获取代理协议 (http, https, socks5)
     */
    public function getProtocol(): string
    {
        return $this->options['protocol'];
    }

    /**
     * 获取自定义参数
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * 设置自定义参数
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
            'host'     => $data['host'] ?? null,
            'port'     => $data['port'] ?? null,
            'username'     => $data['username'] ?? null,
            'password' => $data['password'] ?? null,
            'url'      => $data['url'] ?? null,
            'protocol' => $data['protocol'] ?? null,
            'expiresAt' => $data['expiresAt'] ?? null,
            'metadata' => $data['metadata'] ?? [],
       ]);
    }

    public function generateSessionId(int $length = 8): string
    {
        // 生成安全的随机字符串
        return bin2hex(random_bytes($length / 2));
    }

    public function generateString(array $options,array|string $separator = "_"): string
    {
        $options = Arr::except($this->options, ['username', 'password', 'host', 'port', 'protocol']);

        $options = array_filter($options, fn($value) => !is_null($value));

        if(empty($options)){
            return '';
        }

        $paramStrings = [];
        foreach ($options as $key => $value) {

            if(is_array($value)){
                $value = implode(',', $value);
            }

            if(is_bool($value)){
                $value = (int) $value;
            }

            $paramStrings[] = sprintf('%s-%s', $key, $value);
        }

        return implode($separator, $paramStrings);
    }
}