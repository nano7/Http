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
     * @param $group
     * @param $key
     * @return string
     */
    private function getFlashKey($group, $key = null)
    {
        $str = sprintf('__flash.%s', $group);
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
        return $this->has($this->getFlashKey('old', $key));
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function flash($key, $default = null)
    {
        return $this->get($this->getFlashKey('old', $key), $default);
    }

    /**
     * @param $key
     * @param $values
     */
    public function setFlash($key, $values)
    {
        $values = (array) $values;

        $this->set($this->getFlashKey('new', $key), $values);
    }

    /**
     * Reset flashes.
     */
    public function resetFlashes()
    {
        $newValues = $this->get($this->getFlashKey('new'), []);

        $this->set($this->getFlashKey('old'), $newValues);
        $this->set($this->getFlashKey('new'), []);
    }
}