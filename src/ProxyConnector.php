<?php

namespace Weijiajia\HttpProxyManager;

use Saloon\Http\Connector;
use Weijiajia\HttpProxyManager\Contracts\AccountPasswordInterface;
use Weijiajia\HttpProxyManager\Contracts\DynamicInterface;
use Weijiajia\HttpProxyManager\Data\Proxy;
use Illuminate\Support\Collection;
use Saloon\Http\Request;


abstract class ProxyConnector extends Connector
{
    public function __construct(protected ProxyRequest $request)
    {
        $this->request = $request;
    }

    public function accountPassword(?Request $request = null): Proxy
    {
        $request = $request ?? $this->request;

        if (!$request instanceof AccountPasswordInterface) {
            throw new \InvalidArgumentException('Request must implement AccountPasswordInterface');
        }

        $response = $this->send($request);

        return $request->createDtoFromResponse($response);
    }

    public function dynamic(?Request $request = null): Collection
    {
        $request = $request ?? $this->request;

        if (!$request instanceof DynamicInterface) {
            throw new \InvalidArgumentException('Request must implement DynamicInterface');
        }

        $response = $this->send($request);

        return $request->createDtoFromResponse($response);
    }

    public function default()
    {
        return $this->request instanceof DynamicInterface ? $this->dynamic($this->request) : $this->accountPassword($this->request);
    }
}
