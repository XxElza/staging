<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:48 PM
 */

namespace App\Core\Utils;

use Uuid as UUID;
use Illuminate\Support\Facades\Input;

class File {
    public static function move($files, $targetDir)
    {
        is_dir($targetDir) || @mkdir($targetDir, 0777, true);
        foreach ($files as $value) {
            $newPath = $targetDir . "/" . $value;
            if(is_file($value)){
                rename($value, $newPath);
            }
        }
    }

    /**
     * @param bool $isMulti
     * @return array|null
     * 批量上传图片接口, 也可以单图片上传
     */
    public static function receiveFiles($isMulti = true){
        $files = Input::file("file");
        if(!$isMulti && count($files) > 1){
            return null;
        }
        if(isset($files)){
            $names = [];
            foreach ($files as $file) {
                if(!is_null($file) && $file->isValid()){
                    $ext = $file->getClientOriginalExtension();
                    $newName = UUID::generate() . "." . $ext;
                    $path = config('banma.system.dir.upload');
                    $file->move($path, $newName);
                    $names[] = $newName;
                }
            }
            return $names;
        }
        return null;
    }

    /**
     * @param $originName
     * @return string
     * 单图片上传
     */
    public static function receiveFile($originName)
    {
        $file = Input::file($originName);
        $ext = $file->getClientOriginalExtension();
        $newName = Uuid::generate() . "." . $ext;
        $path = config('banma.system.dir.upload');
        $file->move($path, $newName);
        return $newName;
    }

    public static function chmodr($path, $filemode)
    {
        if (!is_dir($path))
            return chmod($path, $filemode);

        $dh = opendir($path);
        while (($file = readdir($dh)) !== false)
        {
            if($file != '.' && $file != '..')
            {
                try{
                    $fullpath = $path . DIRECTORY_SEPARATOR . $file;
                    if(is_link($fullpath))
                        return FALSE;
                    elseif(!is_dir($fullpath) && !chmod($fullpath, $filemode))
                        return FALSE;
                    elseif(!chmodr($fullpath, $filemode))
                        return FALSE;
                }catch(\Exception $e){
                    $log = ["msg" => "error occured when chmod", "path" => $fullpath];
                    Log::error($log);
                }
            }
        }

        closedir($dh);

        if(chmod($path, $filemode))
            return TRUE;
        else
            return FALSE;
    }

    public static function chmod_R($path, $filePerm = 0644, $dirPerm = 0755) {
        // Check if the path exists
        if(!file_exists($path))
        {
            return(FALSE);
        }
        // See whether this is a file
        if(is_file($path))
        {
            // Chmod the file with our given filepermissions
            chmod($path, $filePerm);
            // If this is a directory...
        } elseif(is_dir($path)) {
            // Then get an array of the contents
            $foldersAndFiles = scandir($path);

            // Remove "." and ".." from the list
            $entries = array_slice($foldersAndFiles, 2);

            // Parse every result...
            foreach($entries as $entry)
            {
                // And call this function again recursively, with the same permissions
                chmod_R($path."/".$entry, $filePerm, $dirPerm);
            }
            // When we are done with the contents of the directory, we chmod the directory itself
            chmod($path, $dirPerm);
        }
        // Everything seemed to work out well, return TRUE
        return(TRUE);
    }

    public static function mkdir_R($path,  $dirPerm = 0777){
        $pathArr = explode('/',$path);

        array_shift($pathArr);
        $dirPath = '';
        foreach ($pathArr as $dir){
            $dirPath .= '/'.$dir;
            if(!is_dir($dirPath)){
                mkdir($dirPath,$dirPerm);
                chmod($dirPath,$dirPerm);
            }
        }
    }

    public static function extension(string $path) : string {
        $info = parse_url($path);
        return pathinfo($info['path'], PATHINFO_EXTENSION);
    }
}