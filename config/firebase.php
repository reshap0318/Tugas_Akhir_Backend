<?php

return [
    
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS'),
        'auto_discovery' => true,
    ],

    'database' => [
        'url' => env('FIREBASE_DATABASE_URL'),

    ],

    'dynamic_links' => [
        'default_domain' => env('FIREBASE_DYNAMIC_LINKS_DEFAULT_DOMAIN')
    ],

    'storage' => [
        'default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET'),
    ],

    'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),

    'logging' => [
        'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL', null),
        'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL', null),
    ],

    'debug' => env('FIREBASE_ENABLE_DEBUG', false),
];
