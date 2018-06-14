<?php namespace Nano7\Http\Routing;

use Nano7\Foundation\Support\Str;
use Nano7\Http\Request;
use Nano7\Foundation\Support\Arr;

class Route
{
    /**
     * @var Router
     */
    protected $router;

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @var string
     */
    protected $uri = '';

    /**
     * @var \Closure
     */
    protected $action;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var array
     */
    protected $middlewares = [];

    /**
     * @var string
     */
    protected $prefixName = '';

    /**
     * @var string|null
     */
    protected $name;

    /**
     * @param $action
     * @param $params
     */
    public function __construct(Router $router, $methods, $uri, $action, $prefixName)
    {
        $this->router  = $router;
        $this->methods = $methods;
        $this->uri     = $uri;
        $this->action  = $action;
        $this->params  = [];
        $this->prefixName = ($prefixName == '') ? '' : $prefixName . '.';
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function run(Request $request)
    {
        $args = array_merge([], [$request], array_values($this->params));

        return call_user_func_array($this->action, $args);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function param($key, $default = null)
    {
        return Arr::get($this->params, $key, $default);
    }

    /**
     * @param $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getUri()
    {
        return $this->uri;
    }

    /**
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * @param null|string|array $middleware
     * @return $this|array
     */
    public function middlewares($middleware = null)
    {
        if (is_null($middleware)) {
            return $this->middlewares;
        }

        if (is_array($middleware)) {
            $this->middlewares = array_merge([], $this->middlewares, $middleware);
        } else {
            $this->middlewares[] = $middleware;
        }

        return $this;
    }

    /**
     * @param string|array $middleware
     * @return $this
     */
    public function middleware($middleware)
    {
        return $this->middlewares($middleware);
    }

    /**
     * @param null $name
     * @return $this|null|string
     */
    public function name($name = null)
    {
        if (is_null($name)) {
            return $this->name;
        }

        $old = $this->name;
        $this->name = ($name == '') ? null : $this->prefixName . $name;

        $this->router->setNames($old, $this->name, $this);

        return $this;
    }

    /**
     * @param array $parameters
     * @return \Nano7\Http\UrlGenerator|string
     * @throws \Exception
     */
    public function url($parameters = [])
    {
        $url = preg_replace_callback('/\{.*?\}/', function ($match) use (&$parameters) {
            return (empty($parameters) && ! Str::endsWith($match[0], '?}'))
                ? $match[0]
                : array_shift($parameters);
        }, $this->uri);

        if (preg_match('/\{.*?\}/', $url)) {
            throw new \Exception("Route erro parameters [$this->uri]");
        }

        return url($url);
    }
}