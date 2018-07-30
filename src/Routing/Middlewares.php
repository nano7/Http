<?php namespace Nano7\Http\Routing;

use ___PHPSTORM_HELPERS\object;
use Closure;
use Nano7\Foundation\Application;
use Nano7\Http\Request;

class Middlewares
{
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
     * @param string|Closure|null $middleware
     */
    public function middleware($alias, $middleware = null)
    {
        if (! array_key_exists($alias, $this->middlewares)) {
            $this->middlewares[$alias] = [];
        }

        if (! is_null($middleware)) {
            $this->middlewares[$alias][] = $middleware;
        }
    }

    /**
     * @return array
     */
    protected function getMiddlewares()
    {
        $list = [];

        foreach ($this->enabledAlias as $alias) {

            // Tratar parametros no alias
            list($alias, $params) = explode(':', $alias);
            $params = is_null($params) ? [] : explode(',', $params);

            if (! array_key_exists($alias, $this->middlewares)) {
                throw new \Exception(sprintf('Alias middleware %s not found', $alias));
            }

            $middleware_list = (array) $this->middlewares[$alias];
            foreach ($middleware_list as $middleware) {
                $m = (object) [
                    'middleware' => $middleware,
                    'params' => $params,
                ];

                $list[] = $m;
            }
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
     * @param $middleware
     * @param $last
     * @return mixed
     */
    protected function runMiddleware($request, $middleware, $last)
    {
        $callback = $middleware->middleware;

        // Se for string mudar para closure
        if (is_string($callback)) {
            $callback = function () use ($middleware) {
                $args = func_get_args();

                $obj = $this->app->make($middleware->middleware);

                return call_user_func_array([$obj, 'handle'], $args);
            };
        }

        // Executar middleware
        if ($callback instanceof Closure) {
            $args = array_merge([], [$request, $last], $middleware->params);

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
    public function getAlias()
    {
        return $this->enabledAlias;
    }
}