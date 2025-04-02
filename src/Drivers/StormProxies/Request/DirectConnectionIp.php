<?php

namespace Weijiajia\HttpProxyManager\Drivers\StormProxies\Request;

use Weijiajia\HttpProxyManager\DirectConnectionIpRequest;
use Weijiajia\HttpProxyManager\ProxyFormat;
class DirectConnectionIp extends DirectConnectionIpRequest
{
    protected ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT;

    public function __construct(array $options = []) {
        parent::__construct($options);

        if (!empty($this->options['sticky_session']) && $this->options['sticky_session']) {

            if(empty($this->options['session']) || !$this->options['session']){
                $this->options['session'] = $this->generateSessionId();
            }

            $this->options['life'] ??= 10;
        }
    }   

    public function getUsername(): string{

        if($generateString = $this->generateString($this->options)){
            return $this->options['username'].'_'.$generateString;
        }
        return $this->options['username'];
    }


    
}
