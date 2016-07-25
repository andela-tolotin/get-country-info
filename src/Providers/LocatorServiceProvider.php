<?php

namespace Wishi\Providers;

use Dotenv\Dotenv as Dotenv;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\ServiceProvider;
use Wishi\Controllers\Locator;

class LocatorServiceProvider extends ServiceProvider
{
    /**
     * [$defer description].
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register application service here.
     *
     * @param void
     *
     * @return Locator
     */
    public function register()
    {
        $this->app->singleton('locator', function ($app) {
            $client = new GuzzleClient();
            $dotenv = new Dotenv(__DIR__.'/../../../../../');

            return new Locator($client, $dotenv);
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @param void
     *
     * @return array
     */
    public function provides()
    {
        return ['locator'];
    }
}
