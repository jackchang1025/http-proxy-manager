<?php

namespace Weijiajia\HttpProxyManager;

use Saloon\Http\Request as SaloonRequest;
use Saloon\Traits\Plugins\AcceptsJson;
use Weijiajia\HttpProxyManager\Contracts\Request as RequestContract;

abstract class Request extends SaloonRequest implements RequestContract
{
    use AcceptsJson;

    public function __construct(
        public array $options = []
     ) {}

     /**
     * 获取请求配置选项
     * 
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }
    
}