<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Oracle Database Connections
    |--------------------------------------------------------------------------
    |
    | Yajra Laravel OCI8 driver allows connecting to one or more Oracle
    | databases. Each entry here is automatically merged into Laravel's
    | config('database.connections') array.
    |
    | Usage:
    |   DB::connection('oracle')->table('...');
    |   DB::connection('oracle2')->table('...');
    |   or in an Eloquent model: protected $connection = 'oracle2';
    |
    */

    'oracle' => [
        'driver'         => 'oracle',
        'tns'            => env('ORACLE_DB_TNS', env('DB_TNS', '')),
        'host'           => env('ORACLE_DB_HOST', env('DB_HOST', '127.0.0.1')),
        'port'           => env('ORACLE_DB_PORT', env('DB_PORT', '1521')),
        'database'       => env('ORACLE_DB_DATABASE', env('DB_DATABASE', '')),
        'service_name'   => env('ORACLE_DB_SERVICE_NAME', env('DB_SERVICE_NAME', '')),
        'username'       => env('ORACLE_DB_USERNAME', env('DB_USERNAME', '')),
        'password'       => env('ORACLE_DB_PASSWORD', env('DB_PASSWORD', '')),
        'charset'        => env('ORACLE_DB_CHARSET', env('DB_CHARSET', 'AL32UTF8')),
        'prefix'         => env('ORACLE_DB_PREFIX', env('DB_PREFIX', '')),
        'prefix_schema'  => env('ORACLE_DB_SCHEMA_PREFIX', env('DB_SCHEMA_PREFIX', '')),
        'schema'         => env('ORACLE_DB_SCHEMA', env('DB_SCHEMA')),
        'edition'        => env('ORACLE_DB_EDITION', 'ora$base'),
        'server_version' => env('ORACLE_DB_SERVER_VERSION', '19c'),
        'load_balance'   => env('ORACLE_DB_LOAD_BALANCE', 'yes'),
        'connect_timeout' => env('ORACLE_DB_CONNECT_TIMEOUT', ''),
        'retry_count'    => env('ORACLE_DB_RETRY_COUNT', '3'),
        'retry_delay'    => env('ORACLE_DB_RETRY_DELAY', '1'),
        'transport_connect_timeout' => env('ORACLE_DB_TRANSPORT_CONNECT_TIMEOUT', '60'),
        'expire_time'    => env('ORACLE_DB_EXPIRE_TIME', '0'),
        'max_name_len'   => env('ORACLE_MAX_NAME_LEN', 30),
        'dynamic'        => [],
        'sessionVars'    => [
            'NLS_TIME_FORMAT'         => 'HH24:MI:SS',
            'NLS_DATE_FORMAT'         => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_FORMAT'    => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
            'NLS_NUMERIC_CHARACTERS'  => '.,',
        ],
    ],

    'oracle2' => [
        'driver'         => 'oracle',
        'tns'            => env('ORACLE2_DB_TNS', ''),
        'host'           => env('ORACLE2_DB_HOST', '127.0.0.1'),
        'port'           => env('ORACLE2_DB_PORT', '1521'),
        'database'       => env('ORACLE2_DB_DATABASE', ''),
        'service_name'   => env('ORACLE2_DB_SERVICE_NAME', ''),
        'username'       => env('ORACLE2_DB_USERNAME', ''),
        'password'       => env('ORACLE2_DB_PASSWORD', ''),
        'charset'        => env('ORACLE2_DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('ORACLE2_DB_PREFIX', ''),
        'prefix_schema'  => env('ORACLE2_DB_SCHEMA_PREFIX', ''),
        'schema'         => env('ORACLE2_DB_SCHEMA'),
        'edition'        => env('ORACLE2_DB_EDITION', 'ora$base'),
        'server_version' => env('ORACLE2_DB_SERVER_VERSION', '19c'),
        'load_balance'   => env('ORACLE2_DB_LOAD_BALANCE', 'yes'),
        'connect_timeout' => env('ORACLE2_DB_CONNECT_TIMEOUT', ''),
        'retry_count'    => env('ORACLE2_DB_RETRY_COUNT', '3'),
        'retry_delay'    => env('ORACLE2_DB_RETRY_DELAY', '1'),
        'transport_connect_timeout' => env('ORACLE2_DB_TRANSPORT_CONNECT_TIMEOUT', '60'),
        'expire_time'    => env('ORACLE2_DB_EXPIRE_TIME', '0'),
        'max_name_len'   => env('ORACLE2_MAX_NAME_LEN', 30),
        'dynamic'        => [],
        'sessionVars'    => [
            'NLS_TIME_FORMAT'         => 'HH24:MI:SS',
            'NLS_DATE_FORMAT'         => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_FORMAT'    => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
            'NLS_NUMERIC_CHARACTERS'  => '.,',
        ],
    ],

    'oracle3' => [
        'driver'         => 'oracle',
        'tns'            => env('ORACLE3_DB_TNS', ''),
        'host'           => env('ORACLE3_DB_HOST', '127.0.0.1'),
        'port'           => env('ORACLE3_DB_PORT', '1521'),
        'database'       => env('ORACLE3_DB_DATABASE', ''),
        'service_name'   => env('ORACLE3_DB_SERVICE_NAME', ''),
        'username'       => env('ORACLE3_DB_USERNAME', ''),
        'password'       => env('ORACLE3_DB_PASSWORD', ''),
        'charset'        => env('ORACLE3_DB_CHARSET', 'AL32UTF8'),
        'prefix'         => env('ORACLE3_DB_PREFIX', ''),
        'prefix_schema'  => env('ORACLE3_DB_SCHEMA_PREFIX', ''),
        'schema'         => env('ORACLE3_DB_SCHEMA'),
        'edition'        => env('ORACLE3_DB_EDITION', 'ora$base'),
        'server_version' => env('ORACLE3_DB_SERVER_VERSION', '19c'),
        'load_balance'   => env('ORACLE3_DB_LOAD_BALANCE', 'yes'),
        'connect_timeout' => env('ORACLE3_DB_CONNECT_TIMEOUT', ''),
        'retry_count'    => env('ORACLE3_DB_RETRY_COUNT', '3'),
        'retry_delay'    => env('ORACLE3_DB_RETRY_DELAY', '1'),
        'transport_connect_timeout' => env('ORACLE3_DB_TRANSPORT_CONNECT_TIMEOUT', '60'),
        'expire_time'    => env('ORACLE3_DB_EXPIRE_TIME', '0'),
        'max_name_len'   => env('ORACLE3_MAX_NAME_LEN', 30),
        'dynamic'        => [],
        'sessionVars'    => [
            'NLS_TIME_FORMAT'         => 'HH24:MI:SS',
            'NLS_DATE_FORMAT'         => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_FORMAT'    => 'YYYY-MM-DD HH24:MI:SS',
            'NLS_TIMESTAMP_TZ_FORMAT' => 'YYYY-MM-DD HH24:MI:SS TZH:TZM',
            'NLS_NUMERIC_CHARACTERS'  => '.,',
        ],
    ],
];
