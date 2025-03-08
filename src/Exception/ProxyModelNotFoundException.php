<?php

namespace Weijiajia\HttpProxyManager\Exception;


class ProxyModelNotFoundException extends \Exception
{
    public function __construct(string $driver)
    {
        parent::__construct("Configuration for driver {$driver} not found.");
    }
}
