<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 4:00 PM
 */

namespace App\Http\Middleware\core;

use App\Core\Utils\User;
use App\Http\Controllers\Controller;
use Closure;
use App\Consts\Response as HttpResponse;

use Illuminate\Support\Facades\Response;

class UserSession
{
    /**
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $noLogin = false;
        $token = $request->header('Authorization');
        if(!isset($token))
            $token = $request->token;
        if(!isset($token) || !User::isValidToken($token)){
            $noLogin = true;
        }


        if($noLogin){
            $arr = [];
            $arr['success'] = false;
            $arr['err_code'] = HttpResponse::NOT_LOGIN;
            $arr['err_msg'] = HttpResponse::ERROR_MESSAGES[HttpResponse::NOT_LOGIN];
            $arr['data'] = null;
            $arr['_ticket'] = Controller::ticket();
            $type = $request->type;
            if($type == 'jsonp'){
                return Response::json($arr)->setCallback($request->callback);
            }
            return Response::json($arr);
        }

        $info = User::decryptToken($token);

        $arr = [
            'uid' => $info['uid']
        ];


        $arr['token'] = $token;

        $request->merge($arr);

        return $next($request);
    }
}