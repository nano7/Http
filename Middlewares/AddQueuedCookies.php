<?php namespace Nano7\Http\Middlewares;

use Illuminate\Http\Request;

class AddQueuedCookies
{
    /**
     * @param Request $request
     * @param \Closure $next
     */
    public function handle(Request $request, $next)
    {
        $response = $next($request);

        foreach (cookie()->getQueuedCookies() as $cookie) {
            $response->headers->setCookie($cookie);
        }

        return $response;
    }
}