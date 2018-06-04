<?php namespace Nano7\Http\Routing;

use FastRoute\RouteCollector;

class RouteCollection
{
    /**
     * @var RouteCollector
     */
    protected $collector;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @param RouteCollector $collector
     * @param string $prefix
     * @param array $middlewares
     */
    public function __construct(RouteCollector $collector, $prefix, $middlewares)
    {
        $this->collector = $collector;
        $this->prefix = $prefix;
        $this->middlewares = $middlewares;
    }

    /**
     * @param Route $route
     * @return Route
     */
    protected function addRoute(Route $route)
    {
        // Adicionar prefixo do group
        $uri = $this->prefix . $route->getUri();

        $this->collector->addRoute($route->getMethods(), $uri, $route);

        // Adicionar middleware do group
        $route->middlewares($this->middlewares);

        return $route;
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute(['GET','HEAD'], $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     * @return Route
     */
    public function get($route, $handler)
    {
        return $this->addRoute(new Route(['GET','HEAD'], $route, $handler));
    }

    /**
     * Adds a POST route to the collection
     *
     * This is simply an alias of $this->addRoute('POST', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     * @return Route
     */
    public function post($route, $handler)
    {
        return $this->addRoute(new Route(['POST'], $route, $handler));
    }

    /**
     * Adds a PUT route to the collection
     *
     * This is simply an alias of $this->addRoute('PUT', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     * @return Route
     */
    public function put($route, $handler)
    {
        return $this->addRoute(new Route(['PUT'], $route, $handler));
    }

    /**
     * Adds a DELETE route to the collection
     *
     * This is simply an alias of $this->addRoute('DELETE', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     * @return Route
     */
    public function delete($route, $handler)
    {
        return $this->addRoute(new Route(['DELETE'], $route, $handler));
    }

    /**
     * Adds a GET route to the collection
     *
     * This is simply an alias of $this->addRoute('*', $route, $handler)
     *
     * @param string $route
     * @param mixed  $handler
     * @return Route
     */
    public function any($route, $handler)
    {
        return $this->addRoute(new Route(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route, $handler));
    }

    /**
     * @param $options
     * @param \Closure $callback
     * @return $this
     */
    public function group($options, \Closure $callback = null)
    {
        // Verificar se foi informado só options como closure
        if (($options instanceof \Closure) && (is_null($callback))) {
            $callback = $options;
            $options = [];
        }

        if (is_string($options)) {
            $options = ['prefix' => $options];
        }

        $prefix = array_key_exists('prefix', $options) ? $options['prefix'] : '';
        $middlewares = array_merge([], array_key_exists('middlewares', $options) ? $options['middlewares'] : []);

        $callback(new RouteCollection($this->collector, $prefix, $middlewares));

        return $this;

        //$this->collector->addGroup($prefix, function($collector) use ($callback, $prefix, $middlewares) {
        //    $callback(new RouteCollection($collector, $prefix, $middlewares));
        //});
    }
}