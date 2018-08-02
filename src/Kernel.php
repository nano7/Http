<?php namespace Nano7\Http;

use Exception;
use Nano7\Http\Request;
use Nano7\Http\Response;
use Nano7\Http\Routing\Router;
use Nano7\Foundation\Application;
use Nano7\Http\Routing\Middlewares;
use Illuminate\Filesystem\Filesystem;

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
        $this->middlewares = new Middlewares($app);
        $this->files = $this->app['files'];
    }

    /**
     * Handle web.
     *
     * @return \Illuminate\Http\JsonResponse|JsonResponse|Response
     */
    public function handle()
    {
        $response = '';

        try {
            // Set running mode
            $this->app->instance('mode', 'web');

            // App boot
            $this->app->boot();

            // Prepare request
            $request = $this->prepareRequest();

            // Preparar rotas
            $response = $this->runRoute($request);

        } catch (\Exception $e) {
            $this->reportException($e);

            $response = Router::toResponse($request, $this->renderException($request, $e));
        }

        // Call terminate
        $this->terminate($request, $response);

        return $response;
    }

    /**
     * @param $request
     * @param $response
     * @return void
     */
    protected function terminate($request, $response)
    {
        event()->fire('web.terminate', [$request, $response]);
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

        // Transferir os middleware groups
        foreach ($this->middlewares->getAllMiddlewareGroups() as $alias => $middleware) {
            $router->middlewareGroup($alias, $middleware);
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
        $request->setSession($this->app['session']);
        //$request->enableHttpMethodParameterOverride();

        $this->app->singleton('Nano7\Http\Request');
        $this->app->alias('request', 'Nano7\Http\Request');
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
     * @param string $alias
     * @param string|\Closure $middleware
     */
    public function middleware($alias, $middleware)
    {
        $this->middlewares->middleware($alias, $middleware);
    }

    /**
     * @param string $alias
     * @param string|\Closure|array $middleware
     */
    public function middlewareGroup($alias, $middleware)
    {
        $this->middlewares->middlewareGroup($alias, $middleware);
    }

    /**
     * Render exception.
     *
     * @param Request $request
     * @param Exception $e
     * @return \Symfony\Component\HttpFoundation\Response|string
     */
    protected function renderException(Request $request, Exception $e)
    {
        return $this->app['Nano7\Foundation\Contracts\Exception\ExceptionHandler']->render($request, $e);
    }

    /**
     * Report Log exception.
     *
     * @param Exception $e
     * @return void
     */
    protected function reportException(Exception $e)
    {
        $this->app['Nano7\Foundation\Contracts\Exception\ExceptionHandler']->report($e);
    }
}