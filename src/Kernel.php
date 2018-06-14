<?php namespace Nano7\Http;

use Nano7\Http\Request;
use Nano7\Http\Response;
use Nano7\Http\Routing\Router;
use Nano7\Foundation\Application;
use Nano7\Http\Routing\Middlewares;
use Illuminate\Filesystem\Filesystem;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Kernel
{
    /**
     * @var Application
     */
    protected $app;

    /**
     * @var Filesystem
     */
    protected $files;

    /**
     * @var Middlewares
     */
    protected $middlewares;

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
        $this->middlewares = new Middlewares();
        $this->files = $this->app['files'];
    }

    /**
     * Handle web.
     */
    public function handle()
    {
        try {
            // Set running mode
            $this->app->instance('mode', 'web');

            // App boot
            $this->app->boot();

            // Prepare request
            $request = $this->prepareRequest();

            // Preparar rotas
            return $this->runRoute($request);

        } catch (HttpException $e) {
            return Router::toResponse($request, $this->renderException($e));
        } catch (\Exception $e) {
            return Router::toResponse($request, sprintf('error: %s', $e->getMessage()));
        }
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|Response
     */
    protected function runRoute(Request $request)
    {
        $router = router();

        // Transferir os middlewares
        foreach ($this->middlewares->getAllMiddlewares() as $alias => $middleware) {
            $router->middleware($alias, $middleware);
        }

        // Transferir os alias
        foreach ($this->middlewares->getAlias() as $alias) {
            $router->alias($alias);
        }

        return $router->handle($request);
    }

    /**
     * Prepare request.
     * @return Request
     */
    protected function prepareRequest()
    {
        $request = Request::capture();
        //$request->setSession(session()->driver());
        $request->enableHttpMethodParameterOverride();

        // Ajustar variaveis para o uso das funcoes url, route, redirect, possam levar para a estrutura nova
        //$vars = $request->server->all();
        //$vars['SCRIPT_NAME'] = str_replace('/runner.php', '/', $vars['SCRIPT_NAME']);
        //$vars['REQUEST_URI'] = str_replace('/runner.php', '/', $vars['REQUEST_URI']);
        //$request->server->replace($vars);

        $this->app->instance('request', $request);

        return $request;
    }

    /**
     * @param $alias
     */
    public function alias($alias)
    {
        $this->middlewares->alias($alias);
    }

    /**
     * @param string|\Closure $middleware
     */
    public function middleware($alias, $middleware)
    {
        $this->middlewares->middleware($alias, $middleware);
    }

    /**
     * @param HttpException $e
     * @return string
     */
    protected function renderException(HttpException $e)
    {
        // Veriifcar se foi implemetado uma view no app
        $view = 'errors.' . $e->getStatusCode();
        if (view()->exists($view)) {
            return view($view)->render();
        }

        // Verificar se foi implememtado uma view no theme
        $view = 'theme::errors.' . $e->getStatusCode();
        if (view()->exists($view)) {
            return view($view)->render();
        }

        return 'error: ' . $e->getStatusCode();
    }
}