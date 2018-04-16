<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/15
 * Time: 22:34
 */

namespace demo\test\model;


use Illuminate\Database\Eloquent\Model;

class FailedBusinessJob extends Model
{
    protected $table = 'failed_log';

    protected $connection = 'global';

    protected $fillable = ['name', 'input', 'output'];

    /**
     * 保存失败的Job信息
     *
     * @param $name
     * @param $input
     * @param $output
     *
     * @return bool
     */
    public function saveInfo($name, $input, $output)
    {
        $this->name = $name;
        $this->input = $input;
        $this->output = $output;
        return $this->save();
    }
}