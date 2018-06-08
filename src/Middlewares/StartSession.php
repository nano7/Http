<?php namespace Nano7\Http\Middlewares;

use Nano7\Http\Request;

class StartSession
{
    /**
     * @param Request $request
     * @param \Closure $next
     */
    public function handle(Request $request, $next)
    {
        session()->setName(config('session.cookie', 'nano_session'));
        session()->start();

        return $next($request);
    }
}