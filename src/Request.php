<?php

namespace Weijiajia\HttpProxyManager;

use Saloon\Http\Request as SaloonRequest;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Contracts\Request as RequestContract;

abstract class Request extends SaloonRequest implements RequestContract
{
    use AcceptsJson;

    
}