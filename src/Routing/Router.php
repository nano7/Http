<?php namespace Nano7\Http\Routing;

use Nano7\View\View;
use Nano7\Http\Request;
use Nano7\Http\Response;
use Nano7\Http\JsonResponse;
use Nano7\Foundation\Application;
use FastRoute\Dispatcher as RouteDispatcher;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Router
{
    /**
     * @var Application
     */
    public $app;

    /**
     * @var Middlewares
     */
    protected $middlewares;

    /**
     * @var RouteDispatcher
     */
    protected $dispatcher;

    /**
     * @var null|Route
     */
    protected $current;

    /**
     * @var array
     */
    protected $allNames = [];

    /**
     * Construtor.
     */
    public function __construct($app)
    {
        $this->app = $app;
        $this->middlewares = new Middlewares();
    }

    /**
     * @param Request $request
     * @return JsonResponse|Response
     */
    public function handle(Request $request)
    {
        $route = $this->findRoute($request);

        //event(new Events\RouteMatched($route, $request));

        // Ativar middlewares da rota
        foreach ($route->middlewares() as $middleware) {
            $this->middlewares->alias($middleware);
        }

        // Executar middlewares
        $response = $this->middlewares->run($request, function(Request $request) use ($route) {
            return $this->prepareResponse($request, $route->run($request));
        });

        return $this->prepareResponse($request, $response);
    }

    /**
     * @return Route|null
     */
    public function current()
    {
        return $this->current;
    }

    /**
     * @param Request $request
     * @return Route
     * @throws \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    protected function findRoute(Request $request)
    {
        $this->prepareRoutes();

        $finded = $this->dispatcher->dispatch($request->method(), $request->getPathInfo());

        if ($finded[0] == RouteDispatcher::NOT_FOUND) {
            throw new NotFoundHttpException;
        }

        if ($finded[0] == RouteDispatcher::METHOD_NOT_ALLOWED) {
            throw new MethodNotAllowedHttpException([$request->method()]);
        }

        $route = $finded[1];
        $route->setParams($finded[2]);

        return $this->current = $route;
    }

    /**
     * Carregar e preparar rotas.
     */
    protected function prepareRoutes()
    {
        $this->dispatcher = \FastRoute\simpleDispatcher(function (\FastRoute\RouteCollector $collector) {
            $router = new RouteCollection($this, $collector, '', [], '');

            $route_file = app_path('routes.php');
            if (file_exists($route_file)) {
                require $route_file;
            }
        });
    }

    /**
     * Create a response instance from the given value.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  mixed  $response
     * @return Response|JsonResponse
     */
    public function prepareResponse($request, $response)
    {
        return static::toResponse($request, $response);
    }

    /**
     * Static version of prepareResponse.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @param  mixed  $response
     * @return Response|JsonResponse
     */
    public static function toResponse($request, $response)
    {
        if ($response instanceof View) {
            $response = $response->render();
        }

        if (! $response instanceof Response) {
            $response = response($response);
        }

        if ($response->getStatusCode() === Response::HTTP_NOT_MODIFIED) {
            $response->setNotModified();
        }

        return $response->prepare($request);
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
     * Find a route by name.
     *
     * @param $name
     * @return null|Route
     */
    public function route($name)
    {
        if (array_key_exists($name, $this->allNames)) {
            return $this->allNames[$name];
        }

        return null;
    }

    /**
     * Set name.
     *
     * @param $old
     * @param $name
     * @param Route $route
     */
    public function setNames($old, $name, Route $route)
    {
        // Verificar se tem old para remover
        if ((! is_null($old)) && (isset($this->allNames[$old]))) {
            unset($this->allNames[$old]);
        }

        if (! is_null($name)) {
            $this->allNames[$name] = $route;
        }
    }
}