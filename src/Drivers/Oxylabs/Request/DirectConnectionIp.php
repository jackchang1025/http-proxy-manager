<?php

namespace Weijiajia\HttpProxyManager\Drivers\Oxylabs\Request;

use Weijiajia\HttpProxyManager\DirectConnectionIpRequest;

class DirectConnectionIp extends DirectConnectionIpRequest
{
    public function __construct(array $options = []) {
        parent::__construct($options);

        if (!empty($this->options['sticky_session']) && $this->options['sticky_session']) {

            if(empty($this->options['session']) || !$this->options['session']){
                $this->options['session'] = $this->generateSessionId();

                $this->options['sesstime'] ??= 10;
            }
        }
    }

    public function getPassword(): string
    {
        if($generateString = $this->generateString($this->options)){
            return $this->options['password'].'_'.$generateString;
        }
        return $this->options['password'];
    }
}