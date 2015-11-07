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

    'permissions' => [
        'table'     => 'permissions',
        'model'     => Arcanedev\LaravelAuth\Models\Permission::class,
    ],

    'user-confirmation' => [
        'enabled'   => false,
    ],

    'throttles' => [
        'enabled'   => true,
        'table'     => 'throttles',
    ],
];
