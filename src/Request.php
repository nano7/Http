<?php namespace Nano7\Http;

use Nano7\Foundation\Support\Str;
use Nano7\Http\Session\StoreInterface;
use Illuminate\Http\Request as BaseRequest;
use Nano7\Http\Concerns\InteractsWithInput;

class Request
{
    use InteractsWithInput;

    /**
     * @var \Illuminate\Http\Request
     */
    protected $base;

    /**
     * @var StoreInterface
     */
    protected $session;

    /**
     * @param BaseRequest $request
     */
    public function __construct(BaseRequest $request)
    {
        $this->base = $request;
    }

    /**
     * Create a new Illuminate HTTP request from server variables.
     *
     * @return static
     */
    public static function capture()
    {
        $base = BaseRequest::capture();

        return new Request($base);
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->base->get($key, $default);
    }

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

    /**
     * @return string
     */
    public function getPathInfo()
    {
        return $this->base->getPathInfo();
    }

    /**
     * Get the request method.
     *
     * @return string
     */
    public function method()
    {
        return $this->base->method();
    }

    /**
     * Get the root URL for the application.
     *
     * @return string
     */
    public function root()
    {
        return $this->base->root();
    }

    /**
     * Get the URL (no query string) for the request.
     *
     * @return string
     */
    public function url()
    {
        return $this->base->url();
    }

    /**
     * Get the full URL for the request.
     *
     * @return string
     */
    public function fullUrl()
    {
        return $this->base->fullUrl();
    }

    /**
     * Get the full URL for the request with the added query string parameters.
     *
     * @param  array  $query
     * @return string
     */
    public function fullUrlWithQuery(array $query)
    {
        return $this->base->fullUrlWithQuery($query);
    }

    /**
     * Get the current path info for the request.
     *
     * @return string
     */
    public function path()
    {
        return $this->base->path();
    }

    /**
     * Get the current encoded path info for the request.
     *
     * @return string
     */
    public function decodedPath()
    {
        return $this->base->decodedPath();
    }

    /**
     * Get a segment from the URI (1 based index).
     *
     * @param  int  $index
     * @param  string|null  $default
     * @return string|null
     */
    public function segment($index, $default = null)
    {
        return $this->base->segment($index, $default);
    }

    /**
     * Get all of the segments for the request path.
     *
     * @return array
     */
    public function segments()
    {
        return $this->base->segments();
    }

    /**
     * Determine if the current request URI matches a pattern.
     *
     * @return bool
     */
    public function is()
    {
        return $this->base->is();
    }

    /**
     * Determine if the current request URL and query string matches a pattern.
     *
     * @return bool
     */
    public function fullUrlIs()
    {
        return $this->base->fullUrlIs();
    }

    /**
     * Determine if the request is over HTTPS.
     *
     * @return bool
     */
    public function secure()
    {
        return $this->base->secure();
    }

    /**
     * Get the client IP address.
     *
     * @return string
     */
    public function ip()
    {
        return $this->base->ip();
    }

    /**
     * Get the client IP addresses.
     *
     * @return array
     */
    public function ips()
    {
        return $this->base->ips();
    }

    /**
     * Get the client user agent.
     *
     * @return string
     */
    public function userAgent()
    {
        return $this->base->userAgent();
    }

    /**
     * @param StoreInterface $session
     */
    public function setSession(StoreInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return StoreInterface
     */
    public function session()
    {
        return $this->session;
    }

    /**
     * @return BaseRequest
     */
    public function getBaseRequest()
    {
        return $this->base;
    }

    /**
     * @return string
     */
    public function getScheme()
    {
        return $this->base->getScheme();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\HeaderBag
     */
    public function headers()
    {
        return $this->base->headers;
    }
}