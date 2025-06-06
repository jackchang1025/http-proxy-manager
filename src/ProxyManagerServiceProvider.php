<?php

namespace Weijiajia\HttpProxyManager;

use Illuminate\Support\ServiceProvider;

class ProxyManagerServiceProvider extends ServiceProvider
{
    /**
     * Boot the application events.
     */
    public function boot(): void
    {
        $this->registerConfig();
    }

    /**
     * Register the service provider.
     */
    public function register(): void {}

    /**
     * Register config.
     */
    protected function registerConfig(): void
    {
        $configPath = __DIR__.'/config/config.php';

        $this->publishes([
            $configPath => config_path('http-proxy-manager.php'),
        ], 'config');

        $this->mergeConfigFrom($configPath, 'http-proxy-manager');
    }
}
