<?php

namespace App\Http\Controllers;

use App\Core\Redis\ALRedis;
use App\Core\Redis\ALRedisTask;
use App\Core\Utils\Utils;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\Consts\Response as HttpResponse;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    function __construct()
    {
        header("Access-Control-Allow-Origin: *");
    }

    public static function ticket()
    {
        $ticket = sha1(Utils::uuid());
        ALRedis::pipeline([
            ALRedisTask::new('set', $ticket, $ticket),
            ALRedisTask::new('expire', $ticket, config('banma.system.sync_token_timeout'))
        ]);
        return $ticket;
    }

    public static function json($data = null){
        $arr = [];
        $arr['success'] = true;
        $arr['err_code'] = -1;
        $arr['err_msg'] = null;
        $arr['data'] = $data;
        $arr['_ticket'] = self::ticket();

        $type = Input::get('type');
        if($type == 'jsonp'){
            return Response::json($arr)->setCallback(Input::get('callback'));
        }
        return Response::json($arr);
    }

    public static function error($code, $msg = '', $redirect = '', $debugMsg = null){
        if(empty($msg) && isset(HttpResponse::ERROR_MESSAGES[$code])){
            $err = HttpResponse::ERROR_MESSAGES[$code];
        }else{
            $err = $msg;
        }

        if(env('APP_DEBUG') && isset($debugMsg)){
            $err = $debugMsg;
        }

        $res = array(
            'err_code' => $code,
            'err_msg' => $err,
            'success' => false,
            'data'=> null,
            '_ticket' => self::ticket()
        );
        if($redirect != '') {
            $res['redirect'] = $redirect;
        }

        $type = Input::get('type');
        if($type == 'jsonp'){
            return Response::json($res)->setCallback(Input::get('callback'));
        }
        return Response::json($res);
    }

    public static function page($data, $start, $length, $total)
    {
        $count = count($data);
        $arr = [
            'success' => true,
            'err_code' => -1,
            'err_msg' => '',
            '_ticket' => self::ticket(),
            'data' => [
                'count' => (int)$count,
                'start' => (int)$start + (int)$count,
                'length' => (int)$length,
                'total' => (int)$total,
                'data' => $data
            ]
        ];

        $type = Input::get('type');
        if($type == 'jsonp'){
            return Response::json($arr)->setCallback(Input::get('callback'));
        }
        return Response::json($arr);
    }
}
