<?php

namespace Weijiajia\HttpProxyManager\Proxys\SmartProxy\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class AccountPassword extends Data
{
    // username 用户名
    // password： 密码
    // session： 不填写:每次请求都提供不同ip；填写:尽可能保证提供相同ip；最大12位
    // life： 保持ip使用的时间,单位分钟，最小1,最大24*60
    // area： 全球地区code 例如：美国 area-US点击查看
    // city： 所属城市 例如：纽约 city-newyork点击查看
    // state： 州代码 例如：纽约 state-Newyork点击查看
    // ip： 指定数据中心地址
    public function __construct(
        //用户名
        public string $username,
        //密码
        public string $password,
        //主机
        public string $host,
        //端口
        public int $port,
        //是否启用粘性会话
        public bool $sticky_session = false,
        //会话ID
        public string $session = '',
        //保持ip使用的时间,单位分钟，最小1,最大24*60
        public int $life = 1,
        //全球地区code 例如：美国 area-US点击查看
        public string $area = '',
        //所属城市 例如：纽约 city-newyork点击查看
        public string $city = '',
        //州代码 例如：纽约 state-Newyork点击查看
        public string $state = '',
        //指定数据中心地址
        public string $ip = '',
    ) {
        if ($this->sticky_session) {
            $this->session ??= $this->generateSessionId();
        }
    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
            'host' => $this->host,
            'port' => $this->port,
            'session' => $this->session,
            'life' => $this->life,
            'area' => $this->area,
            'city' => $this->city,
            'state' => $this->state,
            'ip' => $this->ip,
        ];
    }
}
