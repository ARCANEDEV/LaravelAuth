<?php

return [
    'database'  => [
        'connection'  => null,
    ],

    'users'     => [
        'table'     => 'users',
        'model'     => config('auth.model', Arcanedev\LaravelAuth\Models\User::class),
        'confirm'   => false,
    ],

    'roles'       => [
        'table' => 'roles',
        'model' => Arcanedev\LaravelAuth\Models\Role::class,
    ],

    'permissions'    => [
        'table'  => 'permissions',
        'model'  => Arcanedev\LaravelAuth\Models\Permission::class,
    ],

    'throttles'       => [
        'enabled' => true,
        'table'   => 'throttles',
    ],
];
