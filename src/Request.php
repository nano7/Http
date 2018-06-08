<?php namespace Nano7\Http;

use Nano7\Foundation\Support\Str;

class Request extends \Illuminate\Http\Request
{
    /**
     * @return bool
     */
    public function inApi()
    {
        // Verificar via marcador
        if (app()->resolved('request_in_api') && (app('request_in_api') == true)) {
            return true;
        }

        // Verificar via prefixo
        if (Str::is('/api/*', $this->getPathInfo())) {
            return true;
        }

        return false;
    }
}