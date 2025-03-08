<?php

namespace Weijiajia\HttpProxyManager\Proxys\Oxylabs\Data;

use Weijiajia\HttpProxyManager\Data\Data;

class AccountPassword extends Data
{
    public function __construct(
        public string $username,
        //密码
        public string $password,
        //国家代码，不区分大小写,2 个字母 3166-1 alpha-2 格式。例如，DE 代表德国，GB 代表英国，TH 代表泰国。您可以在此处找到更多关于如何使用特定国家/地区的代理的详情
        public string $cc,
        //城市名称，不区分大小写（英语）。这个参数必须伴随着 cc 参数以提升准确度，例如 cc-GB-city-london 表示英国伦敦；cc-DE-city-berlin 表示德国柏林。对于名称超过 2 个单词的城市，以 _ 代替空格，如 city-st_petersburg 或 city-rio_de_janeiro。我们支持世界上的任何城市，但我们不保证所有城市都有代理。大多数热门城市覆盖率良好，有诸多代理可供选择。您可以下载本表下面所支持城市文件以供参考。点击此处了解更多关于城市级目标的信息。
        public string $city,
        //美国州名不区分大小写，以 us_ 开头，如 us_california、us_illinois。可在本表下面下载所支持州的完整列表。
        public string $st,
        //会话 ID 在接下来的查询中保留相同的 IP。该会话在 10 分钟后到期。之后将为该会话 ID 分配一个新 IP 地址。随机字符串；支持 0-9、A-z 字符。
        public string $sessid,
        //会话持续时间，以分钟为单位。默认值为 10 分钟。
        public int $sesstime = 10,
        //是否启用粘性会话
        public bool $sticky_session = false,
    ) {

    }

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}