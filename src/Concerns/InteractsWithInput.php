<?php namespace Nano7\Http\Concerns;

trait InteractsWithInput
{
    /**
     * Retrieve a server variable from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function server($key = null, $default = null)
    {
        return $this->base->server($key, $default);
    }

    /**
     * Determine if a header is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasHeader($key)
    {
        return $this->base->hasHeader($key);
    }

    /**
     * Retrieve a header from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function header($key = null, $default = null)
    {
        return $this->base->header($key, $default);
    }

    /**
     * Get the bearer token from the request headers.
     *
     * @return string|null
     */
    public function bearerToken()
    {
        return $this->base->bearerToken();
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function exists($key)
    {
        return $this->base->exists($key);
    }

    /**
     * Determine if the request contains a given input item key.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function has($key)
    {
        return $this->base->has($key);
    }

    /**
     * Determine if the request contains any of the given inputs.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function hasAny($keys)
    {
        return $this->base->hasAny($keys);
    }

    /**
     * Determine if the request contains a non-empty value for an input item.
     *
     * @param  string|array  $key
     * @return bool
     */
    public function filled($key)
    {
        return $this->base->filled($key);
    }

    /**
     * Determine if the request contains a non-empty value for any of the given inputs.
     *
     * @param  string|array  $keys
     * @return bool
     */
    public function anyFilled($keys)
    {
        return $this->base->anyFilled($keys);
    }

    /**
     * Get the keys for all of the input and files.
     *
     * @return array
     */
    public function keys()
    {
        return $this->base->keys();
    }

    /**
     * Get all of the input and files for the request.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function all($keys = null)
    {
        return $this->base->all($keys);
    }

    /**
     * Retrieve an input item from the request.
     *
     * @param  string|null  $key
     * @param  string|array|null  $default
     * @return string|array|null
     */
    public function input($key = null, $default = null)
    {
        return $this->base->input($key, $default);
    }

    /**
     * Get a subset containing the provided keys with values from the input data.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function only($keys)
    {
        return $this->base->only($keys);
    }

    /**
     * Get all of the input except for a specified array of items.
     *
     * @param  array|mixed  $keys
     * @return array
     */
    public function except($keys)
    {
        return $this->base->except($keys);
    }

    /**
     * Retrieve a query string item from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function query($key = null, $default = null)
    {
        return $this->base->query($key, $default);
    }

    /**
     * Retrieve a request payload item from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     *
     * @return string|array
     */
    public function post($key = null, $default = null)
    {
        return $this->base->post($key, $default);
    }

    /**
     * Determine if a cookie is set on the request.
     *
     * @param  string  $key
     * @return bool
     */
    public function hasCookie($key)
    {
        return $this->base->hasCookie($key);
    }

    /**
     * Retrieve a cookie from the request.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function cookie($key = null, $default = null)
    {
        return $this->base->cookie($key, $default);
    }
}
