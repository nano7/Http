<?php namespace Nano7\Http\Routing;

use FastRoute\RouteCollector;

class RouteCollection
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var RouteCollector
     */
    protected $collector;

    /**
     * @var string
     */
    protected $prefix = '';

    /**
     * @var string
     */
    protected $name = '';

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @param Router $router
     * @param RouteCollector $collector
     * @param string $prefix
     * @param array $middlewares
     * @param string $name
     */
    public function __construct(Router $router, RouteCollector $collector, $prefix, $middlewares, $name)
    {
        $this->router = $router;
        $this->collector = $collector;
        $this->prefix = $prefix;
        $this->middlewares = $middlewares;
        $this->name = ($name != '') ? $name . '.' : $name;
    }

    /**
     * @param Route $route
     * @return Route
     */
    protected function addRoute(Route $route)
    {
        // Adicionar prefixo do group
        $route->setUri($this->prefix . $route->getUri());

        $this->collector->addRoute($route->getMethods(), $route->getUri(), $route);

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
        return $this->addRoute(new Route($this->router, ['GET','HEAD'], $route, $handler, $this->name));
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
        return $this->addRoute(new Route($this->router, ['POST'], $route, $handler, $this->name));
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
        return $this->addRoute(new Route($this->router, ['PUT'], $route, $handler, $this->name));
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
        return $this->addRoute(new Route($this->router, ['DELETE'], $route, $handler, $this->name));
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
        return $this->addRoute(new Route($this->router, ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route, $handler, $this->name));
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

        $prefix = $this->prefix . (array_key_exists('prefix', $options) ? $options['prefix'] : '');
        $name   = $this->name . (array_key_exists('as', $options) ? $options['as'] : '');
        $middlewares = array_merge([], $this->middlewares, array_key_exists('middlewares', $options) ? $options['middlewares'] : []);

        $callback(new RouteCollection($this->router, $this->collector, $prefix, $middlewares, $name));

        return $this;

        //$this->collector->addGroup($prefix, function($collector) use ($callback, $prefix, $middlewares) {
        //    $callback(new RouteCollection($collector, $prefix, $middlewares));
        //});
    }
}