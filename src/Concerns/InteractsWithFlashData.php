<?php namespace Nano7\Http\Concerns;

trait InteractsWithFlashData
{
    /**
     * Retrieve an old input item.
     *
     * @param  string  $key
     * @param  string|array|null  $default
     * @return string|array
     */
    public function old($key = null, $default = null)
    {
        return $this->session()->old($key, $default);
    }

    /**
     * Flash the input for the current request to the session.
     *
     * @return void
     */
    public function flash()
    {
        $this->session()->setFlash($this->input());
    }

    /**
     * Flush all of the old input from the session.
     *
     * @return void
     */
    public function flush()
    {
        $this->session()->resetFlashes();
    }
}
