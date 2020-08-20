<?php
/**
 * Created by PhpStorm.
 * User: liulei
 * Date: 2020/8/20
 * Time: 4:05 PM
 */

namespace App\Core\Utils;


class SystemCtrlConfig
{
    const Rules = [
        [
            'time_limit'            => 60,
            'limit_times'           => 1000,
            'duration'              => 1800
        ],
        [
            'time_limit'            => 60,
            'limit_times'           => 15000,
            'duration'              => 10800
        ],
        [
            'time_limit'            => 60,
            'limit_times'           => 30000,
            'duration'              => 21600
        ],
        [
            'time_limit'            => 600,
            'limit_times'           => 15000,
            'duration'              => 10800
        ],
        [
            'time_limit'            => 1800,
            'limit_times'           => 55000,
            'duration'              => 21600
        ],
        [
            'time_limit'            => 3600,
            'limit_times'           => 100000,
            'duration'              => 43200
        ]
    ];

    const ActionRules = [
        '$request->route()->getActionName()' => [
            [
                'time_limit'            => 60,
                'limit_times'           => 1000,
                'duration'              => 1800
            ]
        ],
        'App\Http\Controllers\Student\BMIndexController@index' => [
            [
                'time_limit'            => 1800,
                'limit_times'           => 1000,
                'duration'              => 1800
            ]
        ]
    ];
}