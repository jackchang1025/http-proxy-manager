<?php

namespace Weijiajia\HttpProxyManager\Proxys\IpRoyal\Data;

use Weijiajia\HttpProxyManager\Data\Data;


class Residential extends Data
{

    public function __construct(
        //代理用户名
        public string $username,
        //代理密码
        public string $password,
        //主机
        public string $endpoint,
        //端口
        public int $port,
        //配置代理时，考虑最适合您需求的协议非常重要。有两种主要类型可供选择：HTTP/HTTPS 和 SOCKS5。每种协议在其不同的端口集上运行并服务于不同的目的。我们提供两种主要类型的代理协议。
        public string $protocol,
        //是国家配置的键。此参数的值是两个字母的国家代码
        public ?string $country = null,
        //是城市配置的键。此参数的值是城市名称
        public ?string $city = null,
        //state-用于定位美国的某个州。该值应为该州的名称
        public ?string $state = null,
        //isp 用于定位某个位置的特定ISP （Internet 服务提供商）。该值应为提供商的串联名称。
        public ?string $isp = null,
        //是区域配置的关键。添加此值将告诉我们的路由器过滤位于此区域的代理
        public ?string $region = null,

        //是否启用粘性会话
        public bool $sticky_session = false,

        //指示路由器会话保持有效的持续时间
        public int $session_duration = 10,

        //激活“高端代理池”选项后，您将能够访问我们精选的最快、最可靠的代理。但请注意，这种增强质量的代价是可用代理池比通常可访问的代理池要小。
        public ?int $streaming = 1,
        //要启用此功能，您需要添加值为1 的_skipispstatic-键。
        public ?int $skipispstatic = null,
        //IP 跳过功能使您能够编译多个 IP 范围列表，这些列表将在代理连接的 IP 解析过程中自动绕过。要启用此功能，您需要添加_skipipslist-键，该键的值是生成列表的ULID （id）。
        public ?string $skipipslist = null,
        //_session 键指示我们的路由系统为连接创建或解析唯一会话。分配给此键的值必须是随机字母数字字符串，长度恰好为8 个字符。这可确保会话的唯一性和完整性。
        public ?string $session = null,
        //_lifetime-键指示路由器会话保持有效的持续时间。最短持续时间设置为1 秒，最长为7 天。这里的格式非常重要：只能指定一个时间单位。此参数在定义粘性会话的运行跨度、平衡会话稳定性和安全性需求方面起着关键作用。
        public ?string $lifetime = null,
    ) {

        if ($this->sticky_session) {
            $this->session ??= $this->generateSessionId();
            $this->lifetime ??= sprintf('%dm', $this->session_duration);
        }

        if(!in_array($this->protocol, ['http', 'https','socks5'])) {
            throw new \InvalidArgumentException("不支持的代理协议: {$this->protocol}");
        }
    }



    public function toArray(): array
    {
        return [
           'username' => $this->username,
           'password' => $this->password,
           'endpoint' => $this->endpoint,
           'port' => $this->port,
           'protocol' => $this->protocol,
           'country' => $this->country,
           'city' => $this->city,
           'state' => $this->state,
           'isp' => $this->isp,
           'region' => $this->region,
           'sticky_session' => $this->sticky_session,
           'session_duration' => $this->session_duration,
           'streaming' => $this->streaming,
           'skipispstatic' => $this->skipispstatic,
           'skipipslist' => $this->skipipslist,
           'session' => $this->session,
           'lifetime' => $this->lifetime,
        ];
    }
} 