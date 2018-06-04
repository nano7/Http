<?php namespace Nano7\Http;

use Nano7\Foundation\Support\ServiceProvider;

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
        $this->app->singleton('session', function ($app) {
            return new Session();
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