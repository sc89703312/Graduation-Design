<?php

namespace demo\test;

use demo\test\tcc\TCCManager;
use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException as Exception;

class AppServiceProvider extends ServiceProvider
{
    public $app_config;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->serviceInfoRegister();

        $this->callServiceRegister();

//        $this->tccServiceRegister();
    }

    private function serviceInfoRegister()
    {
        $client = new Client([
            // Base URI is used with relative requests
            'base_uri' => 'http://localhost:8500/v1/',
        ]);
        try {
            $register_info = $client->request('GET', 'catalog/service/' . $this->app_config['Name'])->getBody();
            $register_info = json_decode($register_info);
            $has_registered = false;
            foreach($register_info as $info){
                if($info->ServiceID == $this->app_config['ID']){
                    $has_registered = true;
                    break;
                }
            }

            if(!$has_registered){
                $response = $client->request('PUT', 'agent/service/register', [
                    'json' => $this->app_config
                ]);
            }
        }catch (Exception $exception) {
            echo $exception->getMessage();
        }
    }

    private function callServiceRegister()
    {
        $this->app->alias('call.service', CallService::class);
        $this->app->singleton('call.service', function () {
            return new CallService();
        });
    }

    private function tccServiceRegister()
    {
        $this->app->alias('tcc.manager', TCCManager::class);
        $this->app->singleton('tcc.manager', function () {
            return $this->app->make(TCCManager::class);
        });
    }
}
