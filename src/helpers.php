<?php

use Nano7\Http\Request;
use Nano7\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (!function_exists('request')) {
    /**
     * Get an instance of the current request or an input item from the request.
     *
     * @param  string $key
     * @param  mixed $default
     * @return Request|string|array
     */
    function request($key = null, $default = null)
    {
        if (is_null($key)) {
            return app('request');
        }

        return app('request')->input($key, $default);
    }
}

if (!function_exists('response')) {
    /**
     * Return a new response from the application.
     *
     * @param  string $content
     * @param  int $status
     * @param  array $headers
     * @return Response
     */
    function response($content = '', $status = 200, array $headers = [])
    {
        return new Response($content, $status, $headers);
    }
}

if (!function_exists('url')) {
    /**
     * Generate a url for the application.
     *
     * @param  string $path
     * @param  mixed $parameters
     * @param  bool $secure
     * @return \Nano7\Http\UrlGenerator|string
     */
    function url($path = null, $parameters = [], $secure = null)
    {
        if (is_null($path)) {
            return app('url');
        }

        return app('url')->to($path, $parameters, $secure);
    }
}

if (!function_exists('router')) {
    /**
     * @return \Nano7\Http\Routing\Router
     */
    function router()
    {
        return app('router');
    }
}

if (!function_exists('route')) {
    /**
     * @return \Nano7\Http\Routing\Route|null
     */
    function route()
    {
        return router()->current();
    }
}

if (!function_exists('session')) {
    /**
     * @param null $key
     * @param null $default
     * @return mixed|\Nano7\Http\Session
     */
    function session($key = null, $default = null)
    {
        $session = app('session');

        if (is_null($key)) {
            return $session;
        }

        return $session->get($key, $default);
    }
}

if (!function_exists('cookie')) {
    /**
     * @param null $key
     * @param null $default
     * @return \Nano7\Http\CookieManager|string|mixed
     */
    function cookie($key = null, $default = null)
    {
        $cookie = app('cookie');

        if (is_null($key)) {
            return $cookie;
        }

        return $cookie->get($key, $default);
    }
}

if (!function_exists('abort')) {
    /**
     * Throw an HttpException with the given data.
     *
     * @param  Response|int $code
     * @param  string $message
     * @param  array $headers
     * @return void
     *
     * @throws \Symfony\Component\HttpKernel\Exception\HttpException
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    function abort($code, $message = '', array $headers = [])
    {
        if ($code instanceof Response) {
            throw new HttpResponseException($code);
        }

        if ($code == 404) {
            throw new NotFoundHttpException($message);
        }

        throw new HttpException($code, $message, null, $headers);
    }
}