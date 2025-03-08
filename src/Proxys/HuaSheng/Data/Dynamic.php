<?php

namespace Weijiajia\HttpProxyManager\Proxys\HuaSheng\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class Dynamic extends Data
{
    //https://getip.huashengdaili.com/servers.php?session=U7a7284e90723194213--qXYnoeRnl94IOWQ&time=3&count=1&type=text&province=440000&city=440100&pw=no&protocol=http&separator=1&iptype=tunnel&format=null&dev=web
    
    /**
     * time	int	提取的IP时长（1=1分钟，3=3分钟，5=5分钟，10=10分钟，以此类推）
     * count	int	提取的IP数量
     * type	int	返回类型（可选json和text）
     * only	int	是否去重（1=去重，0=不去重），默认不去重
     * province	int	省份编号
     * city	int	城市编号
     * iptype	int	ip类型（tunnel是隧道，direct是直连）
     * pw	int	是否需要账号密码（yes=是，no=否）
     * protocol	int	IP协议（HTTP是HTTP/HTTPS，s5是socks5）
     * separator	int	分隔符样式（1=回车换行(\r\n)，2=回车(\r)；3=换行(\n)；4=Tab(\t)；5=空格( )）
     * format	int	其他返还信息（null是不需要返回城市和IP过期时间；city是返回城市省份；time是返回IP过期时间；city,time是代表返回城市和IP过期时间）
     */
    public function __construct(
        public string $session,
        public int $time = 3,
        public int $count = 1,
        public string $type = 'json',
        public ?string $province = null,
        public ?string $city = null,
        public string $pw = 'no',
        public string $protocol = 'http',
        public int $separator = 1,
        public string $iptype = 'tunnel',
        public string $format = 'city,time',
        public string $dev = 'web',
    ) {
    }

    public function toArray(): array
    {
        return [
            'session' => $this->session,
            'time' => $this->time,
            'count' => $this->count,
            'type' => $this->type,
            'province' => $this->province,
            'city' => $this->city,
            'pw' => $this->pw,
            'protocol' => $this->protocol,
            'separator' => $this->separator,
            'iptype' => $this->iptype,
            'format' => $this->format,
            'dev' => $this->dev,
        ];
    }
}
