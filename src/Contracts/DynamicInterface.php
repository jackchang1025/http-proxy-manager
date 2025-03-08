<?php

namespace Weijiajia\HttpProxyManager\Contracts;

use Saloon\Http\Response;
use Illuminate\Support\Collection;
use Saloon\Http\Request;

interface DynamicInterface
{ 
    public function createDtoFromResponse(Response $response): Collection;
}