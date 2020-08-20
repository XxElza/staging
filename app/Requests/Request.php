<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:39 PM
 */

namespace App\Requests;

use App\Consts\Response;
use Illuminate\Http\Exceptions\HttpResponseException;
use \Illuminate\Contracts\Validation\Validator;

class Request extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            //
        ];
    }

    protected function failedValidation(Validator $validator){
        $arr = ['success' => false];
        $msg = array_first($validator->errors()->toArray())[0];
        $arr['err_msg'] = $msg;
        $arr['err_code'] = Response::PARAM_IS_WRONG;
        $arr['data'] = null;
        throw new HttpResponseException(response()->json($arr, 200));
    }

    public function forbiddenResponse()
    {
        return redirect('noprivilage');
    }
}