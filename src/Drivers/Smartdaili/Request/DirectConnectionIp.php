<?php

namespace Weijiajia\HttpProxyManager\Drivers\Smartdaili\Request;

use Weijiajia\HttpProxyManager\DirectConnectionIpRequest;
use Weijiajia\HttpProxyManager\ProxyFormat;

class DirectConnectionIp extends DirectConnectionIpRequest 
{
    protected ProxyFormat $proxyFormat = ProxyFormat::USER_PASS_AT_HOST_PORT;


    public function __construct(array $options = [])
    {
        parent::__construct($options);

        if (!empty($this->options['sticky_session']) && $this->options['sticky_session']) {

            if(empty($this->options['session']) || !$this->options['session']){
                $this->options['session'] = $this->generateSessionId();
                $this->options['sessionduration'] ??= 10;
            }
        }
    }

    public function getUsername(): string
    {
        $options = $this->options;

        if(isset($options['sticky_session'])){
            unset($options['sticky_session']);
        }
        if(isset($options['mode'])){
            unset($options['mode']);
        }
        
        if($generateString = $this->generateString($options,'-')){
            return "user-".$this->options['username'].'-'.$generateString;
        }
        return "user-".$this->options['username'];
    }
    
}
