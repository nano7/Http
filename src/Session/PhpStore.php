<?php namespace Nano7\Http\Session;

use Nano7\Foundation\Support\Arr;

class PhpStore implements StoreInterface
{
    use Flashes;
    use OldInputs;

    /**
     * @param string|null $name
     * @return string
     */
    public function id($name = null)
    {
        return session_id($name);
    }

    /**
     * @param string|null $name
     * @return string
     */
    public function name($name = null)
    {
        return session_name($name);
    }

    /**
     * @return bool
     */
    public function start()
    {
        return session_start();
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        global $_SESSION;

        return Arr::get($_SESSION, $key, $default);
    }

    /**
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key, $value)
    {
        global $_SESSION;

        return Arr::set($_SESSION, $key, $value);
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {
        global $_SESSION;

        return Arr::has($_SESSION, $key);
    }

    /**
     * Remove key.
     *
     * @param $key
     * @return bool
     */
    public function forget($key)
    {
        global $_SESSION;

        Arr::forget($_SESSION, $key);

        return true;
    }
}