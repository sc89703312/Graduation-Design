<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/10
 * Time: 15:25
 */
return [
    'address' => '127.0.0.1',
    'port' => 8000,
    'ID' => 'Gateway1',
    'Name' => 'Gateway',
    'Tags' => ['urlprefix-/api'],
    'check' => [
        'id' => 'api',
        'name' => 'HTTPAPI on port 8000',
        'http' => 'http://localhost:8000',
        "Status" => "passing",
        'interval' => '20s',
        'timeout' => '10s'
    ],
];