<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 18:13
 */

namespace demo\test\tcc;

use Log;
use demo\test\CallService;

class TCCRpc
{
    public $serviceName;
    public $uri;
    public $method;
    public $params;
    protected $tccStep;
    protected $action;


    public function __construct($serviceName, $uri, $method, $params, $tccStep, $action)
    {
        $this->serviceName = $serviceName;
        $this->uri = $uri;
        $this->method = $method;
        $this->params = $params;
        $this->tccStep = $tccStep;
        $this->action = $action;
    }


    public function data()
    {
        return [
            'service_name'  => $this->serviceName,
            'uri'           => $this->uri,
            'id'            => empty($this->params['id']) ? '-' : $this->params['id'],
            'sequence'      => empty($this->params['seq']) ? '-' : $this->params['seq'],
            'action'        => $this->action,
            'tcc_step'      => $this->tccStep
        ];
    }

    /**
     * 获取 RPC 的信息
     * @return array
     */
    public function getContent()
    {
        return [
            'service_name'  => $this->serviceName,
            'tcc_step'      => $this->tccStep,
            'action'        => $this->action,
            'info'          => $this->params,
            'uri'           => $this->uri,
        ];
    }

    /**
     * @param null $handleBusinessFunc
     * @return mixed
     * @throws \Exception
     */
    public function call($handleBusinessFunc = null)
    {
        try {
            $response = (new CallService())->http($this->serviceName, $this->uri, $this->method, $this->params);
            $data = json_decode($response['content']);
            if (isset($data->code)) {
                if ($data->code == 0) {
                    return $data->data;
                } else if ((!is_null($handleBusinessFunc) && intval($data->code) > 1000)) {
                    return call_user_func($handleBusinessFunc, $data);
                }
            }
            Log::info($response);
            throw new \Exception('TCC Request Error', 400);
        } catch (\Exception $e) {
            Log::info($e);
            throw new \Exception('TCC Request Error', 400);
        }
    }

    public function setParams($params) {
        $this->params = $params;
    }
}