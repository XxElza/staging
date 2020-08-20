<?php
namespace App\Core\utils;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Jenssegers\Agent\Agent;

class Log {
    public static function error($error){
        self::formatLog("Error", $error, self::get_caller_info());
    }

    public static function info($info){
        self::formatLog("Info", $info, self::get_caller_info());
    }

    public static function custom($logName, $str)
    {
        Storage::disk('local')->append($logName, $str);
    }

    private static function formatLog($type, $info, $caller){
        $agent = new Agent();
        if(is_object($info)){
            $info = json_decode($info, true);
        }else{
            $info = [$info];
        }
        $info['platform'] = $agent->platform();
        $info['device'] = $agent->device();
        $info['browser'] = $agent->browser();

        $str = "\n";
        $str .= "------------------------------  " . $type . "  ------------------------------\n";
        $str .= "| Time		:	" . Carbon::now()->format('Y-m-d H:i:s') . "\n";
        $str .= "| Class	:	" . $caller['class'] . "\n";
        $str .= "| Func		:	" . $caller['func'] . "\n";
        $str .= "| Line		:	" . $caller['line'] . "\n";
        $str .= "| File		:	" . $caller['file'] . "\n";
        $str .= "| Content	:	\n| " . json_encode($info, JSON_UNESCAPED_UNICODE) . "\n";
        $str .= "---------------------------------------------------------------------";
        if($type == 'Error'){
            $fileName = self::getLogFileName('error', storage_path('app/logs/error/'));
            Storage::disk('local')->append('logs/error/' . $fileName, $str);
        }else if($type == 'Info'){
            $fileName = self::getLogFileName('info',  storage_path('app/logs/info/'));
            Storage::disk('local')->append('logs/info/' . $fileName, $str);
        }
    }

    private static function get_caller_info() {
        $file = '';
        $func = '';
        $class = '';
        $line = -1;
        $trace = debug_backtrace();
        if (isset($trace[2])) {
            $file = $trace[1]['file'];
            $line = $trace[1]['line'];
            $func = $trace[2]['function'];
            if ((substr($func, 0, 7) == 'include') || (substr($func, 0, 7) == 'require')) {
                $func = '';
            }
        } else if (isset($trace[1])) {
            $file = $trace[1]['file'];
            $line = $trace[1]['line'];
            $func = '';
        }
        if (isset($trace[3]['class'])) {
            $class = $trace[3]['class'];
            $func = $trace[3]['function'];
            $file = $trace[2]['file'];
        } else if (isset($trace[2]['class'])) {
            $class = $trace[2]['class'];
            $func = $trace[2]['function'];
            $file = $trace[1]['file'];
        }
        // if ($file != '') $file = basename($file);
        return ["file" => $file, "func" => $func, "class" => $class, "line" => $line];
    }

    private static function getLogFileName($type, $path)
    {
        $dir = opendir($path);
        $arr = array();
        while($content = readdir($dir)){
            if($content != '.' && $content != '..'){
                $arr[] = $content;
            }
        }
        closedir($dir);
        rsort($arr);
        if (isset($arr[0])){
            $fileArr = explode( '_', $arr[0]);
            // 格式错了就生成一个特别的名字
            if (!isset($fileArr[1]))
                return $type . '_' . Time::nowDateString() . '_'. time(). '.log';

            // 如果日期变了重新生成
            if ($fileArr[1] != Time::nowDateString())
                return $type . '_' . Time::nowDateString() . '_0' . '.log';

            // 如果没达到上限就继续写
            if (filesize($path.$arr[0]) < 1024 * 1024 * 5)
                return $arr[0];

            $index = explode('.', $fileArr[2]);
            return $fileArr[0] . '_' .  $fileArr[1] . '_' . ++$index[0] . '.' .$index[1];
        } else {
            return $type . '_' . Time::nowDateString() . '_0' . '.log';
        }
    }
}
