<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 4:14 PM
 */

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BaseModel extends Model
{
    use SoftDeletes;

}