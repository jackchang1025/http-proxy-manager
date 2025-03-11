<?php

namespace Weijiajia\HttpProxyManager\Proxys\SmartProxy\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class Dynamic extends Data
{
    //app_key: 开放的app_key,可以通过用户个人中心获取
    // pt(可选): 套餐id,提取界面选择套餐可指定对应套餐进行提取
    // num(可选): 单次提取IP数量,最大500
    // cc(可选): 例如俄罗斯：RU，点击查看  全球地区
    // state(可选): 例如：AstrakhanOblast，点击查看  州代码列表
    //city(可选): 例如：Astrakhan，点击查看  城市代码列表
    //life(可选): 单次提取IP时间 最大7200分钟
    //protocol(默认): 代理协议,1.http/socks5
    //format(可选): 返回数据格式,1.txt 2.json
    //lb(可选): 返回数据格式,1.换行回车 2.换行 3.回车 4.Tab
    public function __construct(
        public string $app_key,
        public ?string $pt = null,
        public ?int $num = 1,
        public ?string $cc = null,
        public ?string $state = null,
        public ?string $city = null,
        public ?int $life = 7200,
        public ?string $protocol = 'http',
        public ?string $format = 'json',
        public ?string $lb = '1',
    ) {
        if(!in_array($this->protocol, ['http', 'socks5'])) {
            throw new \InvalidArgumentException("不支持的代理协议: {$this->protocol}");
        }

        if(!in_array($this->format, ['txt', 'json'])) {
            throw new \InvalidArgumentException("不支持的返回数据格式: {$this->format}");
        }

        if(!in_array($this->lb, ['1', '2', '3', '4'])) {
            throw new \InvalidArgumentException("不支持的返回数据格式: {$this->lb}");
        }
    }

    public function toArray(): array
    {
        return [
            'app_key' => $this->app_key,
            'pt' => $this->pt,
            'num' => $this->num,
            'cc' => $this->cc,
            'state' => $this->state,
            'city' => $this->city,
            'life' => $this->life,
            'protocol' => $this->protocol,
            'format' => $this->format,
            'lb' => $this->lb,
        ];
    }
}
