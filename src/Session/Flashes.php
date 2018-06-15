<?php namespace Nano7\Http\Session;

/**
 * Class Flashes
 * @method get($key, $default = null)
 * @method set($key, $value)
 * @method has($key)
 */
trait Flashes
{
    /**
     * @param $key
     * @return string
     */
    private function getFlashKey($key = null)
    {
        $str = '__flash';
        if ($key) {
            $str .= '.' . $key;
        }

        return $str;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasFlash($key)
    {
        return $this->has($this->getFlashKey($key));
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function flash($key, $default = null)
    {
        return $this->get($this->getFlashKey($key), $default);
    }

    /**
     * @param $values
     */
    public function setFlash($values)
    {
        $values = (array) $values;

        $this->set($this->getFlashKey(), $values);
    }

    /**
     * Reset flashes.
     */
    public function resetFlashes()
    {
        $this->setFlash([]);
    }
}