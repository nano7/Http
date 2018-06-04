<?php namespace Nano7\Http\Routing;

use FastRoute\RouteCollector;

class RouteCollection
{
    /**
     * @var RouteCollector
     */
    protected $collector;

    /**
     * @param RouteCollector $collector
     */
    public function __construct(RouteCollector $collector)
    {
        $this->collector = $collector;
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
        $route = new Route(['GET','HEAD'], $route, $handler);

        $this->collector->addRoute($route->getMethods(), $route->getUri(), $route);

        return $route;
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
        $route = new Route(['POST'], $route, $handler);

        $this->collector->addRoute($route->getMethods(), $route->getUri(), $route);

        return $route;
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
        $route = new Route(['PUT'], $route, $handler);

        $this->collector->addRoute($route->getMethods(), $route->getUri(), $route);

        return $route;
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
        $route = new Route(['DELETE'], $route, $handler);

        $this->collector->addRoute($route->getMethods(), $route->getUri(), $route);

        return $route;
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
        $route = new Route(['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'], $route, $handler);

        $this->collector->addRoute($route->getMethods(), $route->getUri(), $route);

        return $route;
    }

    /**
     * @param $prefix
     * @param callable $callback
     */
    public function group($prefix, callable $callback)
    {
        $this->collector->addGroup($prefix, function($collector) use ($callback) {
            $callback(new RouteCollection($collector));
        });
    }
}