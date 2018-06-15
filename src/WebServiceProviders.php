<?php namespace Nano7\Http;

use Nano7\Foundation\Support\ServiceProvider;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBag;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;
use Symfony\Component\HttpFoundation\Session\Attribute\AttributeBag;
use Symfony\Component\HttpFoundation\Session\Storage\PhpBridgeSessionStorage;

class WebServiceProviders extends ServiceProvider
{
    /**
     * Register objetos para web.
     */
    public function register()
    {
        $this->registerKernel();

        $this->registerUrls();

        $this->registerSession();

        $this->registerCookie();

        $this->registerRouting();
    }

    /**
     * Register kernel web.
     */
    public function registerKernel()
    {
        $this->app->singleton('kernel.web', function ($app) {
            $web = new \Nano7\Http\Kernel($app);

            // Carregar middlewares padroes
            $web->middleware('session.start',     '\Nano7\Http\Middlewares\StartSession');
            $web->middleware('cookie.add.queued', '\Nano7\Http\Middlewares\AddQueuedCookies');

            // Carregar alias padrao
            $web->alias('cookie.add.queued');
            $web->alias('session.start');

            // Carregar middlewares
            $middleware_file = app_path('middlewares.php');
            if (file_exists($middleware_file)) {
                require $middleware_file;
            }

            return $web;
        });
    }

    /**
     * Register urls.
     */
    protected function registerUrls()
    {
        $this->app->singleton('url', function () {
            return new UrlGenerator($this->app['request']);
        });
    }

    /**
     * Register routing.
     */
    protected function registerRouting()
    {
        $this->app->singleton('router', function () {
            return new \Nano7\Http\Routing\Router();
        });
    }

    /**
     * Register the session instance.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->app->singleton('session.attributes', function () {
            return new AttributeBag('_nn7_attributes');
        });

        $this->app->singleton('session.flashes', function () {
            return new FlashBag('_nn7_flashes');
        });

        $this->app->singleton('session.storage', function () {
            $handler = new \SessionHandler();

            $bag = new MetadataBag('_nn7_meta');

            return new PhpBridgeSessionStorage($handler, $bag);
        });

        $this->app->singleton('session', function ($app) {
            return new Session($app['session.storage'], $app['session.attributes'], $app['session.flashes']);
        });
    }

    /**
     * Register the cookie instance.
     *
     * @return void
     */
    protected function registerCookie()
    {
        // Registrar cookie
        $this->app->singleton('cookie', function ($app) {
            $config = $app['config']['session'];

            return new CookieManager($config['path'], $config['domain'], $config['secure']);
        });
    }
}