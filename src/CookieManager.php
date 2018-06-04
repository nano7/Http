<?php namespace Nano7\Http;

use Nano7\Foundation\Support\Arr;
use Symfony\Component\HttpFoundation\Cookie;

class CookieManager
{
    /**
     * @var string
     */
    protected $path = '/';

    /**
     * @var string|null
     */
    protected $domain;

    /**
     * @var bool
     */
    protected $secure = false;

    /**
     * All of the cookies queued for sending.
     *
     * @var array
     */
    protected $queued = [];

    /**
     * @param $path
     * @param $domain
     * @param $secure
     */
    public function __construct($path, $domain, $secure)
    {
        $this->path = $path;
        $this->domain = $domain;
        $this->secure = $secure;
    }

    /**
     * Restaurar um valor do cookie.
     *
     * @param $key
     * @param null $default
     * @return null|mixed
     */
    public function get($key, $default = null)
    {
        return request()->cookies->get($key, $default);
    }

    /**
     * Verificar se key existe nos cookies.
     *
     * @param $key
     * @return bool
     */
    public function exists($key)
    {
        return request()->cookies->has($key);
    }

    /**
     * Adicionar cookie na fila.
     *
     * @param $name
     * @param $value
     * @param int $minutes
     * @param null $path
     * @param null $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function set($name, $value = null, $minutes = null, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        $minutes = is_null($minutes) ? config('session.lifetime', 0) : $minutes;

        $this->queue($name, $value, $minutes, $path, $domain, $secure, $httpOnly);

        return true;
    }

    /**
     * Adicionar um cookie na fila eternamente.
     *
     * @param $name
     * @param $value
     * @param null $path
     * @param null $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function forever($name, $value, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        return $this->set($name, $value, 2628000, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Adiciona um cookie na fila para 1 mes.
     *
     * @param $name
     * @param $value
     * @param null $path
     * @param null $domain
     * @param bool $secure
     * @param bool $httpOnly
     * @return bool
     */
    public function month($name, $value, $path = null, $domain = null, $secure = false, $httpOnly = true)
    {
        return $this->set($name, $value, 60 * 24 * 30, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Destruir um cookie.
     *
     * @param $name
     * @param null $path
     * @param null $domain
     * @return bool
     */
    public function forget($name, $path = null, $domain = null)
    {
        return $this->set($name, null, -2628000, $path, $domain);
    }

    /**
     * Create a new cookie instance.
     *
     * @param  string  $name
     * @param  string  $value
     * @param  int     $minutes
     * @param  string  $path
     * @param  string  $domain
     * @param  bool    $secure
     * @param  bool    $httpOnly
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function make($name, $value, $minutes = 0, $path = null, $domain = null, $secure = null, $httpOnly = true)
    {
        $path = is_null($path) ? $this->path : $path;
        $domain = is_null($domain) ? $this->domain : $domain;
        $secure = is_null($secure) ? $this->secure : $secure;

        $time = ($minutes == 0) ? 0 : time() + ($minutes * 60);

        return new Cookie($name, $value, $time, $path, $domain, $secure, $httpOnly);
    }

    /**
     * Determine if a cookie has been queued.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasQueued($key)
    {
        return ! is_null($this->queued($key));
    }

    /**
     * Get a queued cookie instance.
     *
     * @param  string  $key
     * @param  mixed   $default
     * @return \Symfony\Component\HttpFoundation\Cookie
     */
    public function queued($key, $default = null)
    {
        return Arr::get($this->queued, $key, $default);
    }

    /**
     * Queue a cookie to send with the next response.
     *
     * @param  mixed
     * @return void
     */
    public function queue()
    {
        if (head(func_get_args()) instanceof Cookie) {
            $cookie = head(func_get_args());
        } else {
            $cookie = call_user_func_array([$this, 'make'], func_get_args());
        }

        $this->queued[$cookie->getName()] = $cookie;
    }

    /**
     * Remove a cookie from the queue.
     *
     * @param  string  $name
     * @return void
     */
    public function unqueue($name)
    {
        unset($this->queued[$name]);
    }

    /**
     * Get the cookies which have been queued for the next request.
     *
     * @return array
     */
    public function getQueuedCookies()
    {
        return $this->queued;
    }
}