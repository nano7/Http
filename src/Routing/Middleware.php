<?php namespace Nano7\Http\Routing;

abstract class Middleware
{
    /**
     * Register terminate.
     *
     * @param $callback
     * @return void
     */
    protected function terminate($callback)
    {
        event()->listen('web.terminate', $callback);
    }
}