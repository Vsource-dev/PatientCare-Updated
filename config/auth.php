<?php
//config/auth.php
return [
    'defaults' => [
        'guard' => 'web', // you can keep this as `web` for the default login
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'patient' => [
            'driver'   => 'session',
            'provider' => 'patients',
        ],

    ],

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],
         'patients' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Patient::class,
        ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],
];
