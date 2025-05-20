<?php

namespace Weijiajia\HttpProxyManager\Contracts;

use Saloon\Http\Response;

interface Request
{
    public function createDtoFromResponse(Response $response): mixed;
}
