<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:47 PM
 */

namespace App\Core\Utils;

use Uuid as UUID;

class Utils
{
    // 获取新商品id
    public static function build_item_no()
    {
        $uniqId = strtoupper(dechex(date('y'))) . date('m') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99));
        return $uniqId;
    }

    // 获取新优惠券id
    public static function build_coupon_no($sufix = "")
    {
        return strtoupper(base_convert(unique_id(), 10, 35) . base_convert($sufix, 10, 36));
    }

    public static function build_platform_coupon_no($sufix = "")
    {
        return strtoupper('bm' . base_convert(unique_id(), 10, 36) . base_convert($sufix, 10, 36));
    }

    // 获取用户领取店铺优惠券的ID $index为第几张
    public static function build_user_coupon_no($index)
    {
        return build_coupon_no($index);
    }

    // 获取用户领取平台优惠券的ID $index为第几张
    public static function build_user_platform_coupon_no($index)
    {
        return build_platform_coupon_no($index);
    }

    // 获取新订单id
    public static function build_order_no()
    {
        return unique_id((intval(date('Y')) - 2019 + 1));
    }

    //默认最多16位 可加入前后缀，比如服务器，数据分区等进行分布式唯一
    public static function unique_id($prefix = "", $sufix = "")
    {
        $uniqId = $prefix . strtoupper(date('m') + 10) . date('d') . substr(time(), -5) . substr(microtime(), 2, 5) . sprintf('%02d', rand(0, 99)) . $sufix;
        return $uniqId;
    }

    /**
     * 生成指定长度的随机数字
     */
    public static function randomCode($len)
    {
        $seed = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $code = '';
        for ($i = 0; $i < $len; $i++) {
            $num = $seed[rand(0, 9)];
            if ($i == 0 && $num === 0) {
                $num = 1;
            }
            $code .= $num;
        }
        return $code;
    }

    public static function xmlToArray($source)
    {
        if (is_file($source)) { //传的是文件，还是xml的string的判断
            $xml_array = simplexml_load_file($source);
        } else {
            $xml_array = simplexml_load_string($source);
        }

        return objectToArray($xml_array);
    }

    public static function objectToArray($object)
    {
        $object = json_decode(json_encode($object), true);
        return $object;
    }

    // 客户端环境
    public static function clientEnv()
    {
        $http_user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);

        $form_map = [
            'unknown' => 0,
            'android' => 1,
            'ios' => 2,
            'wap' => 3,
            'wechat' => 4,
            'alipay' => 5
        ];

        if (!empty($http_user_agent)) {
            // wechat
            if (strpos($http_user_agent, 'micromessenger') !== false) {
                return $form_map["wechat"];
                // app-ios
            } elseif (strpos($http_user_agent, 'baizu-ios') !== false) {
                return $form_map["ios"];
                // app-android
            } elseif (strpos($http_user_agent, 'baizu-android') !== false) {
                return $form_map["android"];
                // alipay
            } elseif (strpos($http_user_agent, 'alipayclient') !== false) {
                return $form_map["alipay"];
                // wap
            } else {
                return $form_map["wap"];
            }
        }

        return 'unknown';
    }

    public static function uuid()
    {
        return (string)UUID::generate();
    }

    public static function genRandString($length = 6, $numeric = false)
    {

        $str = '0 1 2 3 4 5 6 7 8 9 q w e r t y u i o p a s d f g h j k l z x c v b n m Q W E R T Y U I O P A S D F G H J K L Z X C V B N M';
        if ($numeric) {
            $str = '0 1 2 3 4 5 6 7 8 9';
        }

        $arr = explode(' ', $str);
        shuffle($arr);
        $str = implode('', $arr);
        return substr($str, 0, $length);
    }

}