<?php namespace Nano7\Http\Session;

interface StoreInterface
{
    /**
     * @param string|null $name
     * @return string
     */
    public function id($name = null);

    /**
     * @param string|null $name
     * @return string
     */
    public function name($name = null);

    /**
     * @return bool
     */
    public function start();

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value);

    /**
     * @param $key
     * @return bool
     */
    public function has($key);

    /**
     * Remove key.
     *
     * @param $key
     * @return bool
     */
    public function forget($key);
}