<?php namespace Nano7\Http;

use Nano7\Http\Session\PhpStore;
use Nano7\Foundation\Support\ServiceProvider;

class WebServiceProviders extends ServiceProvider
{
    /**
     * Register objetos para web.
     */
    public function register()
    {
        $this->registerKernel();

        $this->registerResponse();

        $this->registerUrls();

        $this->registerRedirect();

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

            // Executar evento para registro dos middlewares
            event()->fire('web.middleware.register', [$web]);

            // Carregar middlewares
            $middleware_file = app_path('middlewares.php');
            if (file_exists($middleware_file)) {
                require $middleware_file;
            }

            return $web;
        });
    }

    /**
     * Register response factory.
     */
    protected function registerResponse()
    {
        $this->app->singleton('response.factory', function ($app) {
            $factory = new ResponseFactory(
                $app['view'],
                $app['redirect']
            );

            return $factory;
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
     * Register redirect.
     */
    protected function registerRedirect()
    {
        $this->app->singleton('redirect', function ($app) {
            $redirect = new Redirector($app['url']);

            $redirect->setSession($app['session']);

            return $redirect;
        });
    }

    /**
     * Register routing.
     */
    protected function registerRouting()
    {
        $this->app->singleton('router', function ($app) {
            return new \Nano7\Http\Routing\Router($app);
        });
    }

    /**
     * Register the session instance.
     *
     * @return void
     */
    protected function registerSession()
    {
        $this->app->singleton('session', function () {
            return new PhpStore();
        });
        $this->app->alias('session', 'Nano7\Http\Session\StoreInterface');
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