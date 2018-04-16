<?php

namespace App\Providers;

use demo\test\AppServiceProvider as BaseAppServiceProvider;

class AppServiceProvider extends BaseAppServiceProvider
{
    public function register()
    {
        $this->app_config = include dirname(__FILE__) . '/../../config/info.php';
        parent::register();
    }
}

