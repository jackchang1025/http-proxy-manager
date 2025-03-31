<?php

namespace Weijiajia\HttpProxyManager\Drivers\Smartdaili\Request;

use Weijiajia\HttpProxyManager\DirectConnectionIpRequest;
use Weijiajia\HttpProxyManager\ProxyFormat;

class DirectConnectionIp extends DirectConnectionIpRequest 
{
    protected ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT;
    
}
