<?php namespace Nano7\Http\Session;

/**
 * Class Old inputs
 * @method flash($key, $default = null)
 * @method setFlash($key, $value)
 * @method hasFlash($key)
 */
trait OldInputs
{
    /**
     * @param $key
     * @return string
     */
    private function getOldKey($key = null)
    {
        $str = 'olds';
        if ($key) {
            $str .= '.' . $key;
        }

        return $str;
    }

    /**
     * @param $key
     * @return bool
     */
    public function hasOld($key)
    {
        return $this->hasFlash($this->getOldKey($key));
    }

    /**
     * @param $key
     * @param null $default
     * @return mixed
     */
    public function old($key, $default = null)
    {
        return $this->flash($this->getOldKey($key), $default);
    }

    /**
     * @param $values
     */
    public function flashInput($values)
    {
        $values = (array) $values;

        $this->setFlash($this->getOldKey(), $values);
    }
}