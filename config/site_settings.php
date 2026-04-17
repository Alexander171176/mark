<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Source for runtime settings
    |--------------------------------------------------------------------------
    | db        - always from database
    | snapshot  - from storage/app/settings/*.php (fast)
    | cache     - from Cache (redis/memcached/file/etc) [добавим позже]
    */
    'driver' => env('SITE_SETTINGS_DRIVER', 'snapshot'),

    /*
    |--------------------------------------------------------------------------
    | Snapshot paths
    |--------------------------------------------------------------------------
    */
    'snapshot' => [
        'public_path' => storage_path('app/settings/public.php'),
        'admin_path'  => storage_path('app/settings/admin.php'),
    ],
];
