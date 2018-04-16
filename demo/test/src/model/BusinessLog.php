<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 18:41
 */

namespace demo\test\model;

use Illuminate\Database\Eloquent\Model;

class BusinessLog extends DBModel
{
    protected $primaryKey = 'b_id';

    protected $connection = "global";

    protected $table = "business_log";

    protected $fillable=['service_name', 'uri', 'id', 'sequence', 'action', 'tcc_step', 'timestamp'];
}