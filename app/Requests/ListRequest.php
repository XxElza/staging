<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:41 PM
 */

namespace App\Requests;


class ListRequest extends Request
{
    public function rules()
    {
        return [
            'start' => 'nullable|integer|min:0',
            'length' => 'nullable|integer|min:0|max:100'
        ];
    }

    public function messages()
    {
        return [
            'start.integer' => '参数类型错误',
            'length.integer' => '参数类型错误',
            'start.min' => '参数值错误',
            'length.min' => '参数值错误'
        ];
    }
}