<?php

use demo\test\model\UserConfig;
use Illuminate\Http\Response;
use App\Order;
use Illuminate\Http\Request;

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

Route::get('/', function () {
    return 1;
});

Route::group(['prefix' => 'order', 'middleware' => ['base']], function () {
    Route::get('/', function ()    {
        $order = new Order();
        var_dump($order->getViaAllUser());
        return 123;
    });

    Route::post('/info/try_create', function () {
        $order = Order::create(['order_name' => 'hhhhh', 'status' => 'pending', 'seq' => 1]);
        $retData = [
            'id' => $order->id,
            'seq' => $order->seq
        ];
        $ret = [
            'code' => 0,
            'data' => $retData
        ];
        return response()->json($ret);
    });

    Route::post('/info/confirm_create', function (Request $request) {
        $order = Order::find($request->input('id'));
        $order->status = 'success';
        $order->save();
        $retData = [
            'id' => $order->id,
            'seq' => $order->seq
        ];
        $ret = [
            'code' => 0,
            'data' => $retData
        ];
        return response()->json($ret);
    });

    Route::post('/info/cancel_create', function (Request $request)  {
        $order = Order::find($request->input('id'));
        $order->status = 'fail';
        $order->save();
        $retData = [
            'id' => $order->id,
            'seq' => $order->seq
        ];
        $ret = [
            'code' => 0,
            'data' => $retData
        ];
        return response()->json($ret);
    });

    Route::post('/info/try_update', function (Request $request) {
        $order = Order::find($request->input('id'));
        $retData = [
            'id' => $order->id,
            'seq' => $order->seq
        ];
        $ret = [
            'code' => 0,
            'data' => $retData
        ];
        return response()->json($ret);
    });

    Route::post('/info/confirm_update', function (Request $request) {
        $order = Order::find($request->input('id'));
        $order->order_name = $request->input('order_name');
        $order->save();
        $retData = [
            'id' => $order->id,
            'seq' => $order->seq
        ];
        $ret = [
            'code' => 0,
            'data' => $retData
        ];
        return response()->json($ret);
    });

    Route::post('/info/cancel_update', function (Request $request)  {
        $order = Order::find($request->input('id'));
        $retData = [
            'id' => $order->id,
            'seq' => $order->seq
        ];
        $ret = [
            'code' => 0,
            'data' => $retData
        ];
        return response()->json($ret);
    });
});