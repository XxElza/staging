<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 4:06 PM
 */

namespace App\Core\Utils;


class ALArray {
    public static function makeHidden(array $hidden, array $data)
    {
        $res = [];
        foreach($data as $row){
            if(is_array($row)){
                $res[] = array_diff_key($row, array_flip($hidden));
            }
        }
        return $res;
    }

    public static function makeVisible(array $visible, array $data)
    {
        $res = [];
        foreach($data as $row){
            if(is_array($row)){
                $res[] = array_intersect_key($row, array_flip($visible));
            }
        }
        return $res;
    }

    /**
     * $sortFieldAndType为要排序的键与类型，比如 sortField sortType sortField sortType ... sortField sortType，总长度必须为偶数，sortType为 SORT_ASC 或 SORT_DESC
     */
    public static function sort($array, ...$sortFieldAndType) {
        if(!count($array)){
            return $array;
        }
        $arr = [];
        $count = count($sortFieldAndType) / 2;
        for($i = 0; $i<$count; $i++){
            $arr[$i * 2] = [];
            $arr[] = $sortFieldAndType[$i * 2 + 1];
            foreach($array as $key => $value){
                $arr[$i * 2][$key] = $value[$sortFieldAndType[$i * 2]];
            }
        }
        $arr[] = &$array;
        call_user_func_array('array_multisort', $arr);
        return $array;
    }
}