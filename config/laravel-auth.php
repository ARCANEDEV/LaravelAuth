<?php

return [
    'database' => [
        'connection' => config('database.default'),
    ],

    'users' => [
        'table'     => config('auth.table', 'users'),
        'model'     => config('auth.model', Arcanedev\LaravelAuth\Models\User::class),
    ],

    'roles' => [
        'table'     => 'roles',
        'model'     => Arcanedev\LaravelAuth\Models\Role::class,
    ],

    'permissions-groups' => [
        'table'     => 'permissions_groups',
        'model'     => Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
    ],

    'permissions' => [
        'table'     => 'permissions',
        'model'     => Arcanedev\LaravelAuth\Models\Permission::class,
    ],

    'user-confirmation' => [
        'enabled'   => false,
        'length'    => 30,
    ],

    'throttles' => [
        'enabled'   => true,
        'table'     => 'throttles',
    ],

    'slug-separator' => '.',

    'seeds' => [
        'users' => [
            [
                'username'   => 'admin',
                'email'      => env('ADMIN_USER_EMAIL', 'admin@email.com'),
                'password'   => env('ADMIN_USER_PASSWORD', 'password'),
            ],
        ],
    ],
];
