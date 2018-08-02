<?php namespace Nano7\Http\Routing;

use Closure;
use Nano7\Foundation\Application;
use Nano7\Http\Request;

class Middlewares
{
    use MiddlewareNameResolver;

    /**
     * @var Application
     */
    protected $app;

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var array
     */
    protected $middlewareGroups = [];

    /**
     * @var array
     */
    protected $enabledAlias = [];

    /**
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * @param $alias
     */
    public function alias($alias)
    {
        $this->enabledAlias[] = $alias;
    }

    /**
     * @param string|Closure $middleware
     */
    public function middleware($alias, $middleware)
    {
        $this->middlewares[$alias] = $middleware;
    }

    /**
     * @param string|Closure|array $middleware
     */
    public function middlewareGroup($alias, $middleware)
    {
        // Veriifcar se alias de grupo jah foi criado
        if (! array_key_exists($alias, $this->middlewareGroups)) {
            $this->middlewareGroups[$alias] = [];
        }

        // Verificar se foi informado como array
        if (is_array($middleware)) {
            $this->middlewareGroups[$alias] = array_merge([], $this->middlewareGroups[$alias], $middleware);
            return;
        }

        $this->middlewareGroups[$alias][] = $middleware;
    }

    /**
     * @return array
     */
    protected function getMiddlewares()
    {
        $list = [];

        foreach ($this->enabledAlias as $alias) {
            $list = array_merge($list, $this->resolveName($alias));
        }

        return $list;
    }

    /**
     * Executar os middlewares.
     *
     * @param Request $request
     * @param callable $last
     * @return mixed
     */
    public function run(Request $request, Closure $last = null)
    {
        // Ultimo nivel/retorno dos middlewares
        if (is_null($last)) {
            $last = function($request) {
                return true;
            };
        }

        // Montar $next dos middlewares
        $middleares = $this->getMiddlewares();
        for ($i = count($middleares) -1; $i >= 0; $i--) {
            $middleware = $middleares[$i];

            $atual = function($request) use($middleware, $last) {
                return $this->runMiddleware($request, $middleware, $last);
            };
            $last = $atual;
        }

        // Executar
        $response = $last($request);

        return $response;
    }

    /**
     * @param $request
     * @param $rule
     * @param $last
     * @return mixed
     */
    protected function runMiddleware($request, $rule, $last)
    {
        $callback = $rule->middleware;

        // Se for string mudar para closure
        if (is_string($callback)) {
            $callback = function () use ($rule) {
                $args = func_get_args();

                $obj = $this->app->make($rule->middleware);

                return call_user_func_array([$obj, 'handle'], $args);
            };
        }

        // Executar middleware
        if ($callback instanceof Closure) {
            $args = array_merge([], [$request, $last], $rule->params);

            return call_user_func_array($callback, $args);
        }

        // Nao deu para rodar passa para o proximo
        return $last($request);
    }

    /**
     * @return array
     */
    public function getAllMiddlewares()
    {
        return $this->middlewares;
    }

    /**
     * @return array
     */
    public function getAllMiddlewareGroups()
    {
        return $this->middlewareGroups;
    }

    /**
     * @return array
     */
    public function getAlias()
    {
        return $this->enabledAlias;
    }
}