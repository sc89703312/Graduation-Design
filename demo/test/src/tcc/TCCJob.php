<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 18:13
 */

namespace demo\test\tcc;


use Illuminate\Contracts\Queue\ShouldQueue;
use demo\test\model\FailedBusinessJob;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Bus\Queueable;

class TCCJob implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, Queueable;
    /**
     * 最大重试次数
     * @var int
     */
    public $tries = 3;

    /**
     * Job的名称，需在项目内唯一
     * @var string
     */
    public $name = 'tcc_job';

    private $rpc;
    private $log;

    public function __construct(TCCRPC $rpc, TCCLog $log)
    {
        $this->rpc = $rpc;
        $this->log = $log;
    }

    /**
     * @throws \Exception
     */
    public function handle()
    {
        var_dump('???');
//        // 执行rpc请求，业务失败使用 handleBusinessFailed 方法处理
        $this->rpc->call([$this, 'handleBusinessFailed']);
//        // 记录confirm或cancel的流水
        $this->log->write($this->rpc->data());
    }

    /**
     * 处理业务失败
     * @param $data
     */
    public function handleBusinessFailed($data)
    {
        $failedJob = new FailedBusinessJob();
        $failedJob->saveInfo(
            $this->getName(),
            json_encode($this->rpc->getContent()),
            is_string($data) ? $data : json_encode($data)
        );
    }

    /**
     * 获取Job名称
     * @return string
     */
    public function getName()
    {
        return isset($this->name) ? $this->name : self::class;
    }
}