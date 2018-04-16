<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/10
 * Time: 16:25
 */
return [
    'address' => '127.0.0.1',
    'port' => 8002,
    'ID' => 'Goods1',
    'Name' => 'Goods',
    'Tags' => ['goods'],
    'check' => [
        'id' => 'goods',
        'name' => 'HTTPAPI on port 8002',
        'http' => 'http://localhost:8002',
        "Status" => "passing",
        'interval' => '20s',
        'timeout' => '10s'
    ],
];