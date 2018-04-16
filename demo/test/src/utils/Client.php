<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/8
 * Time: 11:35
 */
namespace demo\test\utils;

class Client
{
    protected $a;

    protected $b;

    public function __construct($a, $b)
    {
        $this->a = $a;
        $this->b = $b;
    }

    public function addTogether()
    {
        return $this->a - $this->b;
    }
}