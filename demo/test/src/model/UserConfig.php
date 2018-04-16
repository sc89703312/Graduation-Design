<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/14
 * Time: 11:06
 */

namespace demo\test\model;

use Illuminate\Database\Eloquent\Model;

class UserConfig extends Model
{
    protected $connection = "global";

    protected $table = "user_config";
}