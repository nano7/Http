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
        $request->session()->name(config('session.cookie', 'nano_session'));
        $request->session()->start();

        $response = $next($request);

        $request->session()->resetFlashes();

        return $response;
    }
}