<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:49 PM
 */

namespace App\Core\Utils;


class Env
{
    public static function isDebug(){
        return env("APP_DEBUG");
    }

    public static function isLocal() {
        return env('APP_ENV') === 'local' ?: false;
    }

    public static function isDev() {
        return env('APP_ENV') === 'dev' ?: false;
    }

    public static function isProd() {
        return env('APP_ENV') === 'prod' ?: false;
    }
}