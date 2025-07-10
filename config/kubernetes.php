<?php

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
    | Worker Count
    |--------------------------------------------------------------------------
    |
    | This value defines the number of worker processes that will be started
    | to process incoming requests. If you are using a load balancer, you may
    | want to set this value to the number of CPU cores available.
    |
    */
    'worker_count' => env('KUBERNETES_WORKER_COUNT', 1),

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

    /*
    |--------------------------------------------------------------------------
    | Container mode
    |--------------------------------------------------------------------------
    |
    | This option defines the container mode that gets used when running
    | the Docker container. The name specified in this option should match one
    | of the following modes: "http", "horizon", "worker", "scheduler".
    |
    */
    'container_mode' => env('CONTAINER_MODE', 'http'),

    /*
    |--------------------------------------------------------------------------
    | Project Name
    |--------------------------------------------------------------------------
    |
    | This option defines the name of the project that gets used when running
    | the Docker container. The name specified in this option should match the
    | name of the project that you are running.
    |
    */
    'project_name' => env('PROJECT_NAME', null),

    /*
    |--------------------------------------------------------------------------
    | Metrics
    |--------------------------------------------------------------------------
    |
    | This option defines the configuration for the metrics endpoint that
    | gets used when running the Docker container. The configuration specified
    | in this option should match the configuration of the metrics endpoint.
    |
    */
    'metrics' => [
        'enabled' => env('KUBERNETES_METRICS_ENABLED', true),
        'path' => env('KUBERNETES_METRICS_PATH', 'prometheus'),
        'middleware' => [],
    ],

    'tracing' => [
        'enabled' => env('KUBERNETES_TRACING_ENABLED', true),
        'url' => env('KUBERNETES_TRACING_URL', 'http://jaeger:4318/v1/traces'),
        'traceparent_header' => env('KUBERNETES_TRACING_TRACEPARENT_HEADER', 'traceparent'),
    ]
];