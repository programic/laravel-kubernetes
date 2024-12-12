<?php

use Monolog\Handler\StreamHandler;
use Programic\LaravelKubernetes\Formatter\JsonFormatter;

return [
    /*
    |--------------------------------------------------------------------------
    | File Watching
    |--------------------------------------------------------------------------
    |
    | The following list of files and directories will be watched when using
    | the --watch option offered by Octane. If any of the directories and
    | files are changed, Octane will automatically reload your workers.
    |
    */
    'watch' => [
        'enabled' => env('KUBERNETES_WATCH_ENABLED', env('APP_ENV') === 'local'),
        'paths' => [
            'app',
            'bootstrap',
            'config/**/*.php',
            'database/**/*.php',
            'public/**/*.php',
            'resources/**/*.php',
            'routes',
            'composer.lock',
            '.env',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Maximum Execution Time
    |--------------------------------------------------------------------------
    |
    | The following setting configures the maximum execution time for requests
    | being handled by Octane. You may set this value to 0 to indicate that
    | there isn't a specific time limit on Octane request execution time.
    |
    */
    'max_execution_time' => env('MAX_EXECUTION_TIME', 60),

    'log' => [
        'driver' => 'monolog',
        'level' => env('LOG_LEVEL', 'info'),
        'handler' => StreamHandler::class,
        'formatter' => JsonFormatter::class,
        'with' => [
            'stream' => 'php://stdout',
        ]
    ],

    'tracing' => [
        'enabled' => env('TRACING_ENABLED', env('APP_ENV') !== 'local' && env('APP_ENV') !== 'testing'),
        'driver' => env('TRACING_DRIVER', 'otlp'),
        'drivers' => [
            'otlp' => [
                'endpoint' => env('TRACING_OTLP_ENDPOINT', 'http://localhost:4318/v1/traces'),
            ],
            'zipkin' => [
                'endpoint' => env('TRACING_ZIPKIN_ENDPOINT', 'http://localhost:9411/api/v2/spans'),
            ],
        ]
    ]
];