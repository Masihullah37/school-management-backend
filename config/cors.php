<?php

return [

    'paths' => ['*'],
    // 'paths' => ['api/*', 'sanctum/csrf-cookie', 'login', 'logout', 'register','user','admin/*'],

    'allowed_methods' => ['*'],

    // 'allowed_origins' => ['http://localhost:5173'],
    'allowed_origins' => [env('FRONTEND_URL', 'http://localhost:5173')],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => true,

];