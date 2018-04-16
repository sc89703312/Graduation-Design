<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/14
 * Time: 11:20
 */

namespace demo\test\middleware;

use Closure;
use demo\test\ConfigHelper;

class BaseMiddleware
{

    public function handle($request, Closure $next)
    {
        $this->beforeRequest($request);
        $response = $next($request);
        return $response;
    }

    protected function beforeRequest($request)
    {
        $user_id = $request->input("user_id");
        $configHelper = new ConfigHelper();
        $configHelper->overwriteGlobals();
        try{
            $configHelper->overwriteIndividuals($user_id);
        }catch (\Exception $e) {

        }
    }

}