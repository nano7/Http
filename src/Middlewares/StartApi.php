<?php namespace Nano7\Http\Middlewares;

use Closure;
use Nano7\Http\Request;

class StartApi
{
    /**
     * Handle an incoming request.
     *
     * @param  Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        app()->instance('request_in_api', true);

        return $next($request);
    }
}
