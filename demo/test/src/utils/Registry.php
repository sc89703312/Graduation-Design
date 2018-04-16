<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/10
 * Time: 13:25
 */

namespace demo\test\utils;

class Registry
{
    const LOGGER = 'logger';

    /**
     * @var array
     */
    protected static $storedValues = array();

    /**
     * sets a value
     *
     * @param string $key
     * @param mixed  $value
     *
     * @static
     * @return void
     */
    public static function set($key, $value)
    {
        self::$storedValues[$key] = $value;
    }

    /**
     * gets a value from the registry
     *
     * @param string $key
     *
     * @static
     * @return mixed
     */
    public static function get($key)
    {
        return self::is_valid($key) ? self::$storedValues[$key] : null;
    }

    public static function is_valid($key)
    {
        return array_key_exists($key, self::$storedValues);
    }

    // typically there would be methods to check if a key has already been registered and so on ...
}