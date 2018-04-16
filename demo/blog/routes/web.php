<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use demo\test\tcc\TCCLog;
use demo\test\tcc\TCCManager;
use demo\test\model\BusinessLog;

Route::get('/', function () {
    return 1;
});

Route::group(['prefix' => 'api'], function () {
    Route::get('/', function ()    {

        $tccLog = new TCCLog();
        $tccManager = new TCCManager($tccLog);

        $info = $tccManager->register('order', 'info', 'order:create', ['user_id' => 1])->run();
        var_dump($info);
        return 1;
    });

    Route::get('/update', function ()    {

        $tccLog = new TCCLog();
        $tccManager = new TCCManager($tccLog);

        $info = $tccManager->register('order', 'info', 'order:update', ['user_id' => 1, 'id' => 59, 'order_name' => 'gggg'])->run();
        var_dump($info);
        return 1;
    });
});