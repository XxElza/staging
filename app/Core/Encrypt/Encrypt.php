<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:52 PM
 */

namespace App\Core\Encrypt;

define('SECRET_KEY', 'RRfOdalWheEQcUAsH@YqF6HkeRpfGUzl@PMLLGV3tB5huJ7dibKj5Bf3WXfxf!n7');
define('ENCRYPT_METHOD', 'aes-256-cbc');

class Encrypt
{
    private static function getPrivateKey(){
        return openssl_pkey_get_private(file_get_contents(base_path(config('banma.system.pem.private'))));
    }

    private static function getPubKey(){
        return openssl_pkey_get_public(file_get_contents(base_path(config('banma.system.pem.public'))));
    }

    public static function rsaEncrypt($data){
        if(!is_string($data)){
            return null;
        }
        return openssl_public_encrypt($data, $encrypted, self::getPubKey()) ? base64_encode($encrypted) : null;
    }

    public static function rsaDecrypt($encrypted) {
        if(!is_string($encrypted)){
            return null;
        }
        $encrypted = str_replace(' ', '+', $encrypted);
        return openssl_private_decrypt(base64_decode($encrypted), $decrypted, self::getPrivateKey()) ? $decrypted : null;
    }

    public static function encryptedPassword($encrypted){
        $decrypted = self::rsaDecrypt($encrypted);
        $options = [
            'cost' => 12
        ];
        return password_hash($decrypted, PASSWORD_BCRYPT, $options);
    }

    public static function verifyPassword($encrypted, $pw_from_db){
        $decrypted = self::rsaDecrypt($encrypted);
        return password_verify($decrypted, $pw_from_db);
    }

    /**
     * @param params 为要加密的参数键值对，如 ["key0" => "value0", "key1" => "value1]
     * @param generateUniqueKey 是否生成唯一键值，用于映射加密的参数键值对用
     *
     * 首先，不建议设置$generateUniqueKey为false, 其次，在做映射时建议使用类型Laravel中的Cache方法，以方便切换缓存引擎。
     */
    public static function aesEncrypt(array $params, bool $generateUniqueKey = true){
        $arr = [];
        foreach($params as $key => $value){
            array_push($arr, $key . '=' . $value);
        }
        $data = join('&', $arr);

        $iv = self::generateIV();
        $encrypted = openssl_encrypt($data, ENCRYPT_METHOD, SECRET_KEY, 0, $iv);
        $val = base64_encode(base64_encode($encrypted) . '#' . base64_encode($iv));
        if($generateUniqueKey){
            $key = md5(hash('sha1', $val, true));
            return [
                'key' => $key,
                'value' => $val
            ];
        }
        return $val;
    }

    public static function aesDecrypt(string $encryptedString){
        $val = base64_decode($encryptedString);
        $arr = explode('#', $val);
        if(count($arr) != 2){
            throw new Exception('encrypted string is invalid');
        }

        $encrypted = base64_decode($arr[0]);
        $iv = base64_decode($arr[1]);
        $decrypted = openssl_decrypt($encrypted, ENCRYPT_METHOD, SECRET_KEY, 0, $iv);

        $resArr = explode('&', $decrypted);
        $arr = [];
        foreach($resArr as $value){
            $valueArr = explode('=', $value);
            $arr[$valueArr[0]] = $valueArr[1];
        }
        return $arr;
    }

    private static function generateIV(){
        // generate IV (Initialization Vector)
        $ivLength = openssl_cipher_iv_length(ENCRYPT_METHOD);
        // 就是很牛B
        $isStrong = true;
        $iv = openssl_random_pseudo_bytes($ivLength, $isStrong);
        return $iv;
    }
}