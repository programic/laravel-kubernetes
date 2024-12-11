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
    | Maximum Execution Time
    |--------------------------------------------------------------------------
    |
    | The following setting configures the maximum execution time for requests
    | being handled by Octane. You may set this value to 0 to indicate that
    | there isn't a specific time limit on Octane request execution time.
    |
    */
    'max_execution_time' => env('MAX_EXECUTION_TIME', 60),
];