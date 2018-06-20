<?php namespace Nano7\Http\Middlewares;

use Nano7\Http\Request;
use Nano7\Http\Routing\Middleware;

class StartSession extends Middleware
{
    /**
     * @param Request $request
     * @param \Closure $next
     */
    public function handle(Request $request, $next)
    {
        // Register terminate
        $this->terminate(function($request, $response) {
            $request->session()->resetFlashes();
        });

        $request->session()->name(config('session.cookie', 'nano_session'));
        $request->session()->start();

        return $next($request);
    }
}