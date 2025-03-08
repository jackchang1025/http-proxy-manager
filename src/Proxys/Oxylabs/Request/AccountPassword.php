<?php

namespace Weijiajia\HttpProxyManager\Proxys\Oxylabs\Request;

use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;
use Weijiajia\HttpProxyManager\ProxyRequest;
class AccountPassword extends ProxyRequest implements AccountPasswordInterface
{
    public function resolveEndpoint(): string
    {
        return '/account/password';
    }
}