<?php

return [
    'database' => [
        'connection' => null,
    ],

    'models' => [
        'role'       => Arcanedev\LaravelAuth\Models\Role::class,

        'permission' => Arcanedev\LaravelAuth\Models\Permission::class,
    ],

    'confirm-users' => true,

    'throttles'     => false,
];
