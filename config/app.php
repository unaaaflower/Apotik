<?php

use App\Http\Middleware\Authenticate;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\RedirectIfAuthenticated;

return [
    'name'     => env('APP_NAME', 'KlikMedis'),
    'env'      => env('APP_ENV', 'production'),
    'debug'    => env('APP_DEBUG', false),
    'timezone' => env('APP_TIMEZONE', 'Asia/Jakarta'),

    // Alias middleware yang dipakai di routes/web.php
    'middleware' => [
        'auth'  => Authenticate::class,
        'guest' => RedirectIfAuthenticated::class,
        'admin' => EnsureUserIsAdmin::class,
    ],
];
