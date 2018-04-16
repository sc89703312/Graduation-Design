<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/14
 * Time: 15:10
 */

namespace App;

use demo\test\model\DBModel;

class Order extends DBModel
{
    protected $connection = "individual";

    protected $table = "order";

    protected $fillable = ['order_name', 'status' , 'seq'];

}