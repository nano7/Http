<?php

use Nano7\Http\Request;
use Nano7\Http\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

if (! function_exists('public_path')) {
    /**
     * Get the public application path.
     *
     * @param  string  $path
     * @return string
     */
    function public_path($path = '')
    {
        $path = ($path != '') ? DIRECTORY_SEPARATOR . $path : $path;

        return base_path('public' . $path);
    }
}

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
     * @return \Nano7\Http\ResponseFactory|Response
     */
    function response($content = null, $status = 200, array $headers = [])
    {
        $factory = app('response.factory');
        if (is_null($content)) {
            return $factory;
        }

        return $factory->make($content, $status, $headers);
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

if (! function_exists('redirect')) {
    /**
     * Get an instance of the redirector.
     *
     * @param  string|null  $to
     * @param  int     $status
     * @param  array   $headers
     * @param  bool    $secure
     * @return \Nano7\Http\Redirector|\Nano7\Http\RedirectResponse
     */
    function redirect($to = null, $status = 302, $headers = [], $secure = null)
    {
        if (is_null($to)) {
            return app('redirect');
        }

        return app('redirect')->to($to, $status, $headers, $secure);
    }
}

if (! function_exists('back')) {
    /**
     * Create a new redirect response to the previous location.
     *
     * @param  int    $status
     * @param  array  $headers
     * @param  mixed  $fallback
     * @return \Nano7\Http\RedirectResponse
     */
    function back($status = 302, $headers = [], $fallback = false)
    {
        return app('redirect')->back($status, $headers, $fallback);
    }
}

if (! function_exists('old')) {
    /**
     * Retrieve an old input item.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return mixed
     */
    function old($key = null, $default = null)
    {
        return app('request')->old($key, $default);
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
     * @param string $name
     * @param array $parameters
     * @return string|null
     */
    function route($name, $parameters = [])
    {
        return app('url')->route($name, $parameters);
    }
}

if (!function_exists('session')) {
    /**
     * @param null $key
     * @param null $default
     * @return mixed|string|\Nano7\Http\Session\StoreInterface
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

if (! function_exists('in_api')) {
    /**
     * Return if route is in api group.
     *
     * @return bool
     */
    function in_api()
    {
        // Verificar via marcador
        if (app()->resolved('request_in_api') && (app('request_in_api') == true)) {
            return true;
        }

        // Verificar via prefixo
        if (\Illuminate\Support\Str::is('/api/*', request()->getPathInfo())) {
            return true;
        }

        return false;
    }
}