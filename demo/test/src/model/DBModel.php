<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/14
 * Time: 16:24
 */

namespace demo\test\model;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use demo\test\ConfigHelper;
use demo\test\utils\Registry;

class DBModel extends Model
{

    public $timestamps = false;

    /**
     * @param $user_id
     * @throws \Exception
     */
    public function setUserId($user_id)
    {
        $configHelper = new ConfigHelper();
        $connModifyInfo = $configHelper->overwriteIndividuals($user_id);

        if ($connModifyInfo["modify"]) {
            $this->connection = $this->connection . '_' . $user_id;
        }

        if (!empty($connModifyInfo["db_name"])) {
            $this->table = $connModifyInfo["db_name"] . '.' . $this->table;
        }
    }

    /**
     * @param array $columns
     * @param array $conditions
     * @param null $orderBy
     * @return \Illuminate\Support\Collection
     * @throws \Exception
     */
    public function getViaAllUser($columns = ['*'], $conditions = [], $orderBy = null)
    {
        $result = [];

        $userConfigs = UserConfig::all()->toArray();
        foreach ($userConfigs as $userConfig) {
            // 调整分库
            Registry::set(ConfigHelper::USER_CONFIG_PREFIX . $userConfig['user_id'], $userConfig);
            $this->setUserId($userConfig['user_id']);

            // 数据拼接
            if (is_null($orderBy) || !isset($orderBy['column'])) {
                $result = array_merge($result, DB::connection($this->connection)->table($this->table)->where($conditions)->get($columns)->toArray());
            } else {
                $direction = isset($orderBy['direction']) ? $orderBy['direction'] : 'desc';
                $direction = strtolower($direction) == 'desc' ? 'desc' : 'asc';
                $result = array_merge($result, DB::connection($this->connection)->table($this->table)->where($conditions)->orderBy($orderBy['column'], $direction)->get($columns)->toArray());
            }
        }
        return collect($result);
    }
}