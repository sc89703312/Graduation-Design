<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 18:13
 */

namespace demo\test\tcc;


use demo\test\model\BusinessLog;

class TCCLog
{

    public function write($data)
    {
//        var_dump($data);
//        $businessLog = new BusinessLog();
//        var_dump('??');
//        $businessLog->service_name = $data['service_name'];
//        $businessLog->uri = $data['uri'];
//        $businessLog->id = $data['id'];
//        $businessLog->sequence = $data['sequence'];
//        $businessLog->action = $data['action'];
//        $businessLog->tcc_step = $data['tcc_step'];
//        $businessLog->timestamp = $data['timestamp'];
//        var_dump('????');
//        var_dump($businessLog->save());
        BusinessLog::create($data);
    }

}