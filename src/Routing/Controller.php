<?php namespace Nano7\Http\Routing;

use Nano7\Http\Request;

class Controller
{
    /**
     * Constructor.
     */
    public function __construct()
    {
        //...
    }

    /**
     * @param Request $request
     * @return object
     */
    protected function getJsonContent(Request $request)
    {
        return $request->json();
    }

    /**
     * @param Request $request
     * @return int|string|null
     */
    protected function param(Request $request, $key)
    {
        $value = $request->route()->param($key);
        if (is_null($value)) {
            $this->error('Invalid param ' . $key, 500);
        }

        return $value;
    }

    /**
     * @param $message
     * @param $code
     * @throws \Exception
     */
    protected function error($message, $code)
    {
        throw new \Exception($message, $code);
    }
}