<?php namespace Nano7\Http;

use Illuminate\Support\MessageBag;
use Nano7\Http\Session\StoreInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile as SymfonyUploadedFile;
use Symfony\Component\HttpFoundation\RedirectResponse as BaseRedirectResponse;

class RedirectResponse extends BaseRedirectResponse
{

    /**
     * The request instance.
     *
     * @var Request
     */
    protected $request;

    /**
     * The session store implementation.
     *
     * @var StoreInterface
     */
    protected $session;

    /**
     * Flash a piece of data to the session.
     *
     * @param  string|array  $key
     * @param  mixed  $value
     * @return RedirectResponse
     */
    public function with($key, $value = null)
    {
        $key = is_array($key) ? $key : [$key => $value];

        foreach ($key as $k => $v) {
            $this->session->setFlash($k, $v);
        }

        return $this;
    }

    /**
     * Add multiple cookies to the response.
     *
     * @param  array  $cookies
     * @return $this
     */
    public function withCookies(array $cookies)
    {
        foreach ($cookies as $cookie) {
            $this->headers->setCookie($cookie);
        }

        return $this;
    }

    /**
     * Flash an array of input to the session.
     *
     * @param  array  $input
     * @return $this
     */
    public function withInput(array $input = null)
    {
        $this->session->flashInput($this->removeFilesFromInput(
            ! is_null($input) ? $input : $this->request->input()
        ));

        return $this;
    }

    /**
     * Flash a container of errors to the session.
     *
     * @param  array|MessageBag $errors
     * @param  string|null $message
     * @return $this
     */
    public function withErrors($errors, $message = null)
    {
        if (! $errors instanceof MessageBag) {
            $errors = new MessageBag((array) $errors);
        }

        if (! is_null($message)) {
            $errors->add('__message', $message);
        }

        $this->with('errors', $errors->toArray());

        return $this;
    }

    /**
     * Flash a container of status to the session.
     *
     * @param  string $message
     * @param  string $type
     * @return $this
     */
    public function withStatus($message, $type = 'success')
    {
        $status = [
            'message' => $message,
            'type' => $type,
        ];

        $this->with('status', $status);

        return $this;
    }

    /**
     * Remove all uploaded files form the given input array.
     *
     * @param  array  $input
     * @return array
     */
    protected function removeFilesFromInput(array $input)
    {
        foreach ($input as $key => $value) {
            if (is_array($value)) {
                $input[$key] = $this->removeFilesFromInput($value);
            }

            if ($value instanceof SymfonyUploadedFile) {
                unset($input[$key]);
            }
        }

        return $input;
    }

    /**
     * Get the request instance.
     *
     * @return Request|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set the request instance.
     *
     * @param  Request  $request
     * @return void
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Get the session store implementation.
     *
     * @return StoreInterface|null
     */
    public function getSession()
    {
        return $this->session;
    }

    /**
     * Set the session store implementation.
     *
     * @param  StoreInterface $session
     * @return void
     */
    public function setSession(StoreInterface $session)
    {
        $this->session = $session;
    }
}