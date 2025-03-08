<?php

namespace Weijiajia\HttpProxyManager\Contracts;

use Saloon\Http\Response;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Saloon\Http\Request;

interface AccountPasswordInterface
{
    public function createDtoFromResponse(Response $response): Proxy;
}