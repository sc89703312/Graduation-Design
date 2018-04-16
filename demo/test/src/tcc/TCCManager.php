<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 18:14
 */

namespace demo\test\tcc;

use Log;

class TCCManager
{
    /**
     * 缓存rpc请求的列表
     */
    public $rpcList = [];

    /**
     * 记录需要取消的rpc请求列表
     */
    protected $cancelRpc = [];

    /**
     * 记录需要确认的rpc请求列表
     */
    protected $confirmRpc = [];

    /**
     * 记录前面Try的所有返回结果
     */
    protected $retData = [];

    /**
     * 日志实例
     * @var TCCLog
     */
    protected $log;

    protected $queueKey;

    /**
     * TCCManager constructor.
     *
     * @param TCCLog $log
     */
    public function __construct(TCCLog $log) {
        $this->log = $log;
        $this->queueKey = 'tcc.queue';
    }

    public function register($service, $uri, $name, $params) {
        // 注册进来
        $method = 'post';
        $this->rpcList[] = [$service, $uri, $name, $method, $params];
        return $this;
    }

    protected function generateUrls($resource, $action) {
        return ["/{$resource}/try_{$action}", "/{$resource}/confirm_{$action}", "/{$resource}/cancel_{$action}"];
    }

    /**
     * @param $rpc
     */
    protected function handleQueue($rpc) {
        $job = new TCCJob($rpc, $this->log);
        dispatch($job->onQueue($this->queueKey));
    }

    /**
     * 确认处理
     */
    protected function confirm() {
        foreach ($this->confirmRpc as $rpc) {
            // 执行confirm的rpc
            $this->handleQueue($rpc);
        }
    }

    /**
     * 取消处理
     */
    protected function cancel() {
        foreach ($this->cancelRpc as $rpc) {
            // 执行cancel的rpc
            $this->handleQueue($rpc);
        }
    }

    protected function addConfirmRpc($service, $confirmUrl, $params, $action, $method)
    {
        $rpc = $this->generateRpc($service, $confirmUrl, $method, $params, Constant::TCC_STEP_CONFIRM, $action);
        $this->confirmRpc[] = $rpc;
    }

    protected function addCancelRpc($service, $cancelUrl, $params, $action, $method)
    {
        $rpc = $this->generateRpc($service, $cancelUrl, $method, $params, Constant::TCC_STEP_CANCEL, $action);
        $this->cancelRpc[] = $rpc;
    }

    protected function generateRpc($serviceName, $uri, $method, $params, $tccStep, $action)
    {
        return new TCCRpc($serviceName, $uri, $method, $params, $tccStep, $action);
    }

    /**
     * @return array|bool
     * @throws \Exception
     */
    public function run() {
        try {
            foreach ($this->rpcList as $rpc) {
                list($service, $uri, $name, $method, $params) = $rpc;
                if (is_callable($params)) {
                    $params = $params($this->retData);
                }
                list($resource, $action) = array_pad(explode(':', $name), 2, '');

                list($tryUrl, $confirmUrl, $cancelUrl) = $this->generateUrls($uri, $action);
                $tryRpc = $this->generateRpc($service, $tryUrl, $method, $params, Constant::TCC_STEP_TRY, $action);

                $responseBody = $tryRpc->call();

                if ($action == Constant::ACTION_CREATE && !empty($responseBody->id)) {
                    // 是创建操作需要将try生成的ID作为cancel和confirm的参数传入
                    $params['id'] = $responseBody->id;
                }

                if(!empty($responseBody->seq)) {
                    if ($action != Constant::ACTION_CREATE) {
                        $params['seq'] = $responseBody->seq + 1;
                    } else {
                        $params['seq'] = $responseBody->seq;
                    }
                }

                // 更新rpc中的params
                $tryRpc->setParams($params);

                // 记录try的流水
                $this->log->write($tryRpc->data());

                $this->retData[] = $responseBody;
                $this->addConfirmRpc($service, $confirmUrl, $params, $action, $method);
                $this->addCancelRpc($service, $cancelUrl, $params, $action, $method);
            }
            var_dump($this->confirmRpc);
            var_dump($this->cancelRpc);
            $this->confirm();
            return $this->retData;
        } catch (\Exception $e) {
            Log::error($e);
            $this->cancel();
            return false;
        }
    }
}