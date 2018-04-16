<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/10
 * Time: 16:05
 */
return [
    'address' => '127.0.0.1',
    'port' => 8001,
    'ID' => 'Order1',
    'Name' => 'Order',
    'Tags' => ['urlprefix-/order'],
    'check' => [
        'id' => 'order',
        'name' => 'HTTPAPI on port 8001',
        'http' => 'http://localhost:8001',
        "Status" => "passing",
        'interval' => '20s',
        'timeout' => '10s'
    ],
];