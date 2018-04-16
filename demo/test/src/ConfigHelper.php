<?php
/**
 * Created by PhpStorm.
 * User: Echo
 * Date: 2018/4/14
 * Time: 11:10
 */

namespace demo\test;

use Illuminate\Support\Facades\Redis;
use demo\test\utils\Registry;
use demo\test\model\UserConfig;

class ConfigHelper
{
    private $redis = null;

    const EXPIRES_TIME_SECOND = 14400;
    const USER_CONFIG_PREFIX = "User:Config:";

    public function overwriteGlobals($key='database.connections')
    {
        $overConfig = [];
        $dbConfig = config($key);

        foreach ($dbConfig as $configId => $subConfig) {
            if ( !(isset($subConfig['ons_mode']) && $subConfig['ons_mode']) ) {
                if ( isset($subConfig['zk_host']) ) {
                    $zkHostConfig = $this->zkname($subConfig['zk_host']);
                    if (!empty($zkHostConfig)) {
                        $overConfig[$key . '.' . $configId . '.host'] = $zkHostConfig['ip'];
                        $overConfig[$key . '.' . $configId . '.port'] = $zkHostConfig['port'];
                    }
                }
            }
        }

        $config = empty($overConfig) ? null : $overConfig;
        if (!empty($config)) {
            config($config);
        }
    }

    /**
     * @param $user_id
     * @param string $key
     * @return array
     * @throws \Exception
     */
    public function overwriteIndividuals($user_id, $key="database.connections")
    {
        $connModifyFlag = false;
        $connModifyDB = null;

        $overConfig = [];
        $dbConfig = config($key);

        foreach ($dbConfig as $configId => $subConfig) {
            if (isset($subConfig['ons_mode']) && $subConfig['ons_mode']) {
                // 需要查找分库表
                if (is_numeric($user_id)) {
                    // 用户id非空
                    $configInfo = $this->getUserONS($user_id);
                    $zk_host_tmp = $configInfo['ons_name'];
                    $db_name_tmp = $configInfo['db_name'];

                    // 处理配置中的连接
                    $zkHostConfig = $this->zkname($zk_host_tmp);
                    if (!empty($zkHostConfig)) {
                        // 名字服务信息存在
                        if (empty($subConfig['host']) || empty($subConfig['database'])) {
                            // connection的host db未配置
                            $overConfig[$key . '.' . $configId . '.host'] = $zkHostConfig['ip'];
                            $overConfig[$key . '.' . $configId . '.port'] = $zkHostConfig['port'];
                            $overConfig[$key . '.' . $configId . '.database'] = $db_name_tmp;
                        } else {
                            // connection的host db已经配置
                            if ($zkHostConfig['ip'] != $subConfig['host'] || $zkHostConfig['port'] != $subConfig['port']) {
                                // ip或者port不同 需要建立不同的连接
                                $overConfig[$key . '.' . $configId . '_' . $user_id] = $subConfig;
                                $overConfig[$key . '.' . $configId . '_' . $user_id . '.host'] = $zkHostConfig['ip'];
                                $overConfig[$key . '.' . $configId . '_' . $user_id . '.port'] = $zkHostConfig['port'];
                                $overConfig[$key . '.' . $configId . '_' . $user_id . '.database'] = $db_name_tmp;
                                $connModifyFlag = true;
                            } else if ($db_name_tmp != $subConfig['database']) {
                                // ip或者port相同 但是db_name不同 需要修改connection的数据库
                                $connModifyDB = $db_name_tmp;
                            }
                        }
                    }
                } else {
                    // Do nothing ......
                    continue;
                }
            }
        }

        $config = empty($overConfig) ? null : $overConfig;
        // 调整配置
        if (!empty($config)) {
            config($config);
        }

        return [
            "modify"    =>    $connModifyFlag,
            "db_name"   =>    $connModifyDB
        ];
    }

    /**
     * @param $user_id
     * @return array|mixed
     * @throws \Exception
     */
    private function getUserONS($user_id)
    {
        $redis = $this->getRedis();
        $redisKey = $this->getRedisKey($user_id);

        if (!is_null(Registry::get($redisKey))) {
            return Registry::get($redisKey);
        }

        if ($redis->exists($redisKey)) {
            $cache_info = $redis->hMGet($redisKey, ["user_id", 'ons_name', 'db_name']);
            //兼容predis或者phpredis
            if(isset($cache_info["user_id"]) && $cache_info["user_id"]) {
                $config_info = [
                    "user_id"    =>     isset($cache_info["user_id"]) ? $cache_info["user_id"] : null,
                    "ons_name"   =>     isset($cache_info["ons_name"]) ? $cache_info['ons_name'] : null,
                    "db_name"    =>     isset($cache_info["db_name"]) ? $cache_info["db_name"] : null,
                ];
            } else {
                $config_info = [
                    "user_id"    =>     isset($cache_info[0]) ? $cache_info[0] : null,
                    "ons_name"   =>     isset($cache_info[1]) ? $cache_info[1] : null,
                    "db_name"    =>     isset($cache_info[2]) ? $cache_info[2] : null,
                ];
            }
            if(!is_numeric($config_info['user_id'])) {
                throw new \Exception("用户id " . $user_id . " 账户配置信息未查询到", 500);
            }
            Registry::set($redisKey, $config_info);
            return $config_info;
        } else {
            $user_config_info = new UserConfig();
            $result = $user_config_info->where([["user_id", $user_id]])->first();

            if (is_null($result)) {
                throw new \Exception("用户id " . $user_id . " 账户配置信息未查询到", 500);
            } else {
                $result_arr = $result->toArray();
                $redis->hMSet($redisKey, $result_arr);
                $redis->expire($redisKey, self::EXPIRES_TIME_SECOND);
                Registry::set($redisKey, $result_arr);
                return $result;
            }
        }
    }

    private function getRedisKey($user_id)
    {
        return self::USER_CONFIG_PREFIX . $user_id;
    }

    private function getRedis()
    {
        if (is_null($this->redis)) {
            $this->redis = Redis::connection();
        }
        return $this->redis;
    }

    public function zkname($zk_host)
    {
        // 本地由于没有ons服务，以固定的ip:port表示名字对应的地址
        $res = [];
        $res['ip'] = '127.0.0.1';
        $res['port'] = 3306;
        return $res;
    }
}