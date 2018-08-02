<?php namespace Nano7\Http\Routing;

/**
 * Class MiddlewareNameResolver
 * @package Nano7\Http\Routing
 * @property array $middlewares
 * @property array $middlewareGroups
 */
trait MiddlewareNameResolver
{
    /**
     * @param $name
     * @return array
     */
    protected function resolveName($name)
    {
        // Verificar se eh um group
        if (isset($this->middlewareGroups[$name])) {
            return $this->parseMiddlewareGroup($name);
        }

        // Tratar alias com parametros
        list($middleware, $params) = array_pad(explode(':', $name), 2, null);
        $params = is_null($params) ? [] : explode(',', $params);

        if (! array_key_exists($middleware, $this->middlewares)) {
            throw new \Exception(sprintf('Middleware name %s not found', $middleware));
        }

        $m = (object) [
            'middleware' => $this->middlewares[$middleware],
            'params' => $params,
        ];

        return [$m];
    }

    /**
     * @param $name
     * @return array
     */
    protected function parseMiddlewareGroup($name)
    {
        $results = [];

        foreach ($this->middlewareGroups[$name] as $middleware) {
            $results = array_merge($results, $this->resolveName($middleware));
        }

        return $results;
    }
}