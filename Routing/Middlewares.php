<?php namespace Nano7\Http\Routing;

use Closure;
use Illuminate\Http\Request;

class Middlewares
{
    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var array
     */
    protected $enabledAlias = [];

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
     * @return array
     */
    protected function getMiddlewares()
    {
        $list = [];

        foreach ($this->enabledAlias as $alias) {
            if (! array_key_exists($alias, $this->middlewares)) {
                throw new \Exception(sprintf('Alias middleware %s not found', $alias));
            }

            $list[] = $this->middlewares[$alias];
        }

        return $list;
    }

    /**
     * Executar os middlewares da estrutura antiga.
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
        // Se for string mudar para closure
        if (is_string($middleware)) {
            $middleware = function ($request, $next) use ($middleware) {
                $obj = new $middleware($this);

                return $obj->handle($request, $next);
            };
        }

        // Executar middleware
        if ($middleware instanceof Closure) {
            return $middleware($request, $last);
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