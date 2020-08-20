<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 3:50 PM
 */

namespace App\Core\Redis;


class ALRedisTask
{
    var $command;
    var $data;

    public function __construct(string $command, ...$data)
    {
        $this->command = $command;
        $this->data = $data;
    }

    public static function new(string $command, ...$data) : ALRedisTask {
        return new ALRedisTask($command, ...$data);
    }
}