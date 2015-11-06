<?php

return [
    'database'  => [
        'connection'  => null,
    ],

    'models'    => [
        'role'        => Arcanedev\LaravelAuth\Models\Role::class,

        'permission'  => Arcanedev\LaravelAuth\Models\Permission::class,
    ],

    'tables'    => [
        'users'       => 'users',

        'roles'       => 'roles',

        'permissions' => 'permissions',

        'throttles'   => 'throttles',
    ],

    'confirm-users'   => true,

    'throttles'       => false,
];
