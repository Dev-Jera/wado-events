<?php

use Illuminate\Support\Str;

return [

    'domain' => env('HORIZON_DOMAIN'),
    'path'   => env('HORIZON_PATH', 'horizon'),

    'driver' => env('QUEUE_CONNECTION', 'redis'),
    'use'    => 'default',

    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_') . '_horizon:'),

    'middleware' => ['web'],

    'waits' => [
        'redis:tickets'       => 5,   // alert if ticket queue backs up > 5 s
        'redis:notifications' => 30,  // email/SMS can wait a bit longer
        'redis:default'       => 60,
    ],

    'trim' => [
        'recent'        => 60,
        'pending'       => 60,
        'completed'     => 60,
        'recent_failed' => 10080, // keep failed jobs for 7 days
        'failed'        => 10080,
        'monitored'     => 10080,
    ],

    'silenced' => [],

    'metrics' => [
        'trim_snapshots' => [
            'job'   => 24,
            'queue' => 24,
        ],
    ],

    'fast_termination' => false,

    'memory_limit' => 256,

    'defaults' => [
        'supervisor-tickets' => [
            'connection'       => 'redis',
            'queue'            => ['tickets'],
            'balance'          => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'     => 5,
            'maxTime'          => 0,
            'maxJobs'          => 0,
            'memory'           => 256,
            'tries'            => 4,
            'timeout'          => 120,
            'nice'             => 0,
        ],

        'supervisor-notifications' => [
            'connection'       => 'redis',
            'queue'            => ['notifications'],
            'balance'          => 'auto',
            'autoScalingStrategy' => 'time',
            'maxProcesses'     => 3,
            'maxTime'          => 0,
            'maxJobs'          => 0,
            'memory'           => 256,
            'tries'            => 3,
            'timeout'          => 90,
            'nice'             => 5,
        ],

        'supervisor-default' => [
            'connection'       => 'redis',
            'queue'            => ['default'],
            'balance'          => 'simple',
            'maxProcesses'     => 2,
            'maxTime'          => 0,
            'maxJobs'          => 0,
            'memory'           => 256,
            'tries'            => 3,
            'timeout'          => 60,
            'nice'             => 10,
        ],
    ],

    'environments' => [
        'production' => [
            'supervisor-tickets' => [
                'minProcesses' => 2,
                'maxProcesses' => 5,
            ],
            'supervisor-notifications' => [
                'minProcesses' => 1,
                'maxProcesses' => 3,
            ],
            'supervisor-default' => [
                'minProcesses' => 1,
                'maxProcesses' => 2,
            ],
        ],

        'local' => [
            'supervisor-tickets' => [
                'minProcesses' => 1,
                'maxProcesses' => 2,
            ],
            'supervisor-notifications' => [
                'minProcesses' => 1,
                'maxProcesses' => 1,
            ],
            'supervisor-default' => [
                'minProcesses' => 1,
                'maxProcesses' => 1,
            ],
        ],
    ],
];
