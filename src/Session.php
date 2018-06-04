<?php namespace Nano7\Http;

use Nano7\Foundation\Support\Arr;

class Session
{
    /**
     * Get the name of the session.
     *
     * @return string
     */
    public function getName()
    {
        return session_name();
    }

    /**
     * Set the name of the session.
     *
     * @param string $name
     * @return void
     */
    public function setName($name)
    {
        session_name($name);
    }

    /**
     * Get the current session ID.
     *
     * @return string
     */
    public function getId()
    {
        return session_id();
    }

    /**
     * Set the session ID.
     *
     * @param  string $id
     * @return void
     */
    public function setId($id)
    {
        session_id($id);
    }

    /**
     * Start the session, reading the data from a handler.
     *
     * @return bool
     */
    public function start()
    {
        return session_start();
    }

    /**
     * Save the session data to storage.
     *
     * @return bool
     */
    public function save()
    {
        session_save_path();

        return true;
    }

    /**
     * Get all of the session data.
     *
     * @return array
     */
    public function all()
    {
        global $_SESSION;

        return $_SESSION;
    }

    /**
     * Checks if a key exists.
     *
     * @param  string|array $key
     * @return bool
     */
    public function exists($key)
    {
        return array_key_exists($key, $this->all());
    }

    /**
     * Get an item from the session.
     *
     * @param  string $key
     * @param  mixed $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return Arr::get($this->all(), $key, $default);
    }

    /**
     * Put a key / value pair or array of key / value pairs in the session.
     *
     * @param  string|array $key
     * @param  mixed $value
     * @return void
     */
    public function put($key, $value = null)
    {
        global $_SESSION;

        Arr::set($_SESSION, $key, $value);
    }

    /**
     * Remove key.
     *
     * @param  $key
     * @return void
     */
    public function remove($key)
    {
        global $_SESSION;

        unset($_SESSION[$key]);
    }

    /**
     * Determine if the session has been started.
     *
     * @return bool
     */
    public function isStarted()
    {
        return (trim(session_id()) != '');
    }
}