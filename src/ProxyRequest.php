<?php

namespace Weijiajia\HttpProxyManager;

use Saloon\Http\Request;
use Saloon\Traits\Plugins\AcceptsJson;

abstract class ProxyRequest extends Request
{
    use AcceptsJson;

    
} 