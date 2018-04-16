<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/13
 * Time: 10:57
 */

namespace demo\test\utils;


class RequestInfoUtil
{
    /**
     * 单位秒
     * @return float
     */
    public static function getTimes()
    {
        list($usec, $sec) = explode(' ', microtime());
        return ((float)$usec + (float)$sec);
    }

    /**
     * 单位M
     * @param int $precision
     *
     * @return float
     */
    public static function getMemory($precision = 3)
    {
        return round(memory_get_usage() / 1024 / 1024, $precision);
    }
}