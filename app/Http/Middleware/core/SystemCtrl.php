<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 4:03 PM
 */

namespace App\Http\Middleware\core;

use App\Core\Redis\ALRedis;
use App\Core\Redis\ALRedisTask;
use App\Core\Utils\ALArray;
use App\Core\utils\Log;
use App\Core\Utils\SystemCtrlConfig;
use App\Core\Utils\Time;
use Closure;
use App\Consts\Response as HttpResponse;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Response;



/**
 * TODO: 增加小黑屋逻辑，多次触发系统管控的用户，关入小黑屋，在小黑屋中的用户，在一定时间内将一直到处于管控状态。
 */
class SystemCtrl
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $isPost = $request->isMethod('post');
        if($isPost && $request->route()->getName() != 'captchaCheck' && $request->route()->getName() != 'login'){
            // 当天23:59:59 回收所有管控信息
            $nowDate = Time::now();
            $curYear = $nowDate->year;
            $curMonth = $nowDate->month;
            $curDay = $nowDate->day;
            $expireAt = Time::parse("$curYear-$curMonth-$curDay 23:59:59")->timestamp;

            $ip = $request->ip();
            $uid = $request->uid;
            // 存在用户id则使用用户id为标识，否则使用ip
            $key = isset($uid) ? $uid : $ip;

            $actionName = $request->route()->getActionName();
            $dateString = Time::nowDateString();
            // 先判断系统级是否被触发，再看个人是否被触发，如果已经触发系统级，则跳过个人级检测

            // 系统级 系统接口级 个人接口级 个人全系统级
            $systemLevelKey = 'system_level_control_' . $dateString;
            $systemActionLevelKey = 'system_action_level_control_' . $actionName;
            $personalSystemLevelKey = 'personal_level_control_' . $key;
            $personalLevelKey = 'personal_action_level_control_' . $actionName . '_' . $key;

            $systemCtrlInfoKey = 'in_system_control_' . $dateString;
            $actionSystemCtrlInfoKey = 'in_action_system_control_' . $dateString;
            $personalSystemCtrlInfoKey = 'in_personal_system_control_' . $dateString;
            $personalActionCtrlInfoKey = 'in_personal_action_control_' . $dateString;

            $nowUnix = Time::nowTimestamp();
            $res = ALRedis::pipeline([
                ALRedisTask::new('hgetall', $systemLevelKey),
                ALRedisTask::new('hgetall', $systemActionLevelKey),
                ALRedisTask::new('hgetall', $personalSystemLevelKey),
                ALRedisTask::new('hgetall', $personalLevelKey),
                // 读取系统或个人管控信息
                ALRedisTask::new('get', $systemCtrlInfoKey),
                ALRedisTask::new('get', $actionSystemCtrlInfoKey),
                ALRedisTask::new('get', $personalSystemCtrlInfoKey),
                ALRedisTask::new('get', $personalActionCtrlInfoKey),

                // 系统级初始化
                ALRedisTask::new('hsetnx', $systemLevelKey, 'start', $nowUnix),
                ALRedisTask::new('hincrby', $systemLevelKey, 'times', 1),
                ALRedisTask::new('expireat', $systemLevelKey, $expireAt),

                // 系统接口级初始化
                ALRedisTask::new('hsetnx', $systemActionLevelKey, 'start', $nowUnix),
                ALRedisTask::new('hsetnx', $systemActionLevelKey, 'actionName', $actionName),
                ALRedisTask::new('hincrby', $systemActionLevelKey, 'times', 1),
                ALRedisTask::new('expireat', $systemActionLevelKey, $expireAt),

                // 个人级
                ALRedisTask::new('hsetnx', $personalSystemLevelKey, 'start', $nowUnix),
                ALRedisTask::new('hincrby', $personalSystemLevelKey, 'times', 1),
                ALRedisTask::new('expireat', $personalSystemLevelKey, $expireAt),

                // 个人接口级
                ALRedisTask::new('hsetnx', $personalLevelKey, 'start', $nowUnix),
                ALRedisTask::new('hsetnx', $personalLevelKey, 'actionName', $actionName),
                ALRedisTask::new('hincrby', $personalLevelKey, 'times', 1),
                ALRedisTask::new('expireat', $personalLevelKey, $expireAt)
            ]);

            // 接口系统级
            $ruleOfActionName = isset(SystemCtrlConfig::ActionRules[$actionName]) ? SystemCtrlConfig::ActionRules[$actionName] : [];
            $systemConf = ALArray::sort(SystemCtrlConfig::Rules, 'time_limit', SORT_ASC, 'limit_times', SORT_ASC);
            $systemActionConf = ALArray::sort($ruleOfActionName, 'time_limit', SORT_ASC, 'limit_times', SORT_ASC);

            $resetInterval = config('banma.system.control_reset_interval');
            $tasks = [];
            $resultArr = [];

            $isUnderCtrl = false;

            $levelKeys = [$systemLevelKey, $systemActionLevelKey, $personalSystemLevelKey, $personalLevelKey];
            $infoKeys = [$systemCtrlInfoKey, $actionSystemCtrlInfoKey, $personalSystemCtrlInfoKey, $personalActionCtrlInfoKey];
            $configs = [$systemConf, $systemActionConf, $systemConf, $systemActionConf];
            $personalIndexes = [2, 3];

            $underCtrlResp = [
                'success' => false,
                'err_code' => HttpResponse::UNDER_CTRL,
                'err_msg' => HttpResponse::ERROR_MESSAGES[HttpResponse::UNDER_CTRL],
                'data' => null
            ];

            if($res[4] || $res[5] || $res[6] || $res[7]){
                $resultArr = $underCtrlResp;
                $isUnderCtrl = true;
            }else{
                for($i = 0; $i<4; $i++){
                    if(count($res[$i])){
                        $start = $res[$i]['start'];
                        $times = $res[$i]['times'] + 1;

                        // 超出时间，重置，然后什么都不做
                        if($nowUnix - $start > $resetInterval){
                            $tasks[] = ALRedisTask::new('hset', $levelKeys[$i], 'start', $nowUnix);
                            $tasks[] = ALRedisTask::new('hset', $levelKeys[$i], 'times', 1);
                        }else{
                            $config = $this->getConfig($nowUnix - $start, array_search($i, $personalIndexes) === false ? $times : $times / config('banma.system.personal_control_ratio'), $configs[$i]);
                            if(isset($config)){
                                $tasks[] = ALRedisTask::new('set', $infoKeys[$i], 1);
                                $tasks[] = ALRedisTask::new('expire', $infoKeys[$i], $config['duration']);

                                $resultArr = $underCtrlResp;
                                $isUnderCtrl = true;
                                break;
                            }
                        }
                    }
                }
            }
            if(count($tasks)){
                ALRedis::pipeline($tasks);
            }

            // for testing
//            $isUnderCtrl = true;
//            $resultArr = $underCtrlResp;

            if($isUnderCtrl){
                $vc = null;
                if(isset($_COOKIE['vc'])){
                    $vc = $_COOKIE['vc'];
                    Log::info("vc in cookie: $vc");
                }else{
                    Log::info('no vc in cookie');
                }
                if(!isset($vc) || !Cache::has("$vc") || Cache::get("$vc") != 1){
                    Log::info([
                        'msg' => 'under control',
                        'params' => [
                            $resultArr,
                            $request->all()
                        ]
                    ]);
                    return Response::json($resultArr);
                }
                // 需要注意一点，当用户直接发起Captcha验证之后，在失效时间内触发管控时，由于存在vc信息，第一次管控将可能直接被通过。
                if(isset($vc)){
                    Cache::forget("$vc");
                }
            }
        }

        return $next($request);
    }

    private function getConfig($time, $currentTimes, $configArray) {
        foreach($configArray as $value){
            if($time <= $value['time_limit'] && $currentTimes >= $value['limit_times']){
                return $value;
            }
        }
        return null;
    }
}