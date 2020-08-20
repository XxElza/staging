<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:46 PM
 */

namespace App\Core\Utils;


use App\Core\Encrypt\Encrypt;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class User {
    /**
     * 生成返回给前端的 time key参数
     */
    public static function genKeyTime($uid){
        $now = Carbon::now()->timestamp;
        $key = self::genLoginKey($uid, $now);
        return [
            "time" => $now,
            "key" => $key
        ];
    }

    /**
     * 生成登录的key，注册时直接调用genKeyTime即可。
     * 此方法主要用于登录后调用接口时的验证
     * 如：
     * Input::get("key") == genLoginKey($uid, $time)
     */
    public static function genLoginKey($uid, $time){
        return md5($uid . $time . substr($uid, 0, 6) . substr($uid, -6));
    }

    /**
     * 获取通过手机号进入的用户uid
     * $registTime = micro()
     */
    public static function getUid($autoIncrementIdFromDB, $registTime){
        return self::genUid($autoIncrementIdFromDB, $registTime, 1);
    }

    /**
     * 获取通过微信登录的用户的uid
     * $registTime = micro()
     */
    public static function getUidFromWechat($autoIncrementIdFromDB, $registTime){
        return self::genUid($autoIncrementIdFromDB, $registTime, 2);
    }

    /**
     * 获取通过微博登录的用户的uid
     * $registTime = micro()
     */

    public static function getUidFromWeibo($autoIncrementIdFromDB, $registTime){
        return self::genUid($autoIncrementIdFromDB, $registTime, 3);
    }

    private static function genUid($autoIncrementIdFromDB, $registTime, $type){
        $hash = hash('sha1', "$autoIncrementIdFromDB$registTime$type");
        return $hash;
    }

    /**
     * @param string $uid
     * @param string $key
     * @param int $time
     * @param int $timeout 分钟
     */
    public static function getToken(array $params, int $timeout = 10){
        $res = Encrypt::aesEncrypt($params);
        Cache::put($res['key'], $res['value'], $timeout);
        return $res['key'];
    }

    public static function isValidToken(?string $token){
        return $token ? Cache::has($token) : null;
    }

    public static function decryptToken(string $token) {
        if(!self::isValidToken($token)){
            return false;
        }
        $value = Cache::get($token);
        $params = Encrypt::aesDecrypt($value);
        return $params;
    }

    public static function kickOffline(string $token){
        Log::info([
            'msg' => 'kick offline',
            'params' => $token
        ]);
        if(Cache::has($token)){
            Cache::forget($token);
        }
        return true;
    }

    public static function clientIp() {
        $cip = "unknown";
        if ($_SERVER['REMOTE_ADDR']) {
            $cip = $_SERVER['REMOTE_ADDR'];
        } elseif (getenv('REMOTE_ADDR')) {
            $cip = getenv('REMOTE_ADDR');
        }
        return $cip;
    }
}