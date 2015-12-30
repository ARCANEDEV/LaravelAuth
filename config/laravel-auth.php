<?php

return [
    /* ------------------------------------------------------------------------------------------------
     |  Database
     | ------------------------------------------------------------------------------------------------
     */
    'database'           => [
        'connection' => config('database.default'),
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Models
     | ------------------------------------------------------------------------------------------------
     */
    'users'              => [
        'table'    => 'users',
        'model'    => Arcanedev\LaravelAuth\Models\User::class,
        'observer' => Arcanedev\LaravelAuth\Observers\UserObserver::class,
    ],

    'roles'              => [
        'table'    => 'roles',
        'model'    => Arcanedev\LaravelAuth\Models\Role::class,
        'observer' => Arcanedev\LaravelAuth\Observers\RoleObserver::class,
    ],

    'permissions-groups' => [
        'table'    => 'permissions_groups',
        'model'    => Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
        'observer' => Arcanedev\LaravelAuth\Observers\PermissionsGroupObserver::class,
    ],

    'permissions'        => [
        'table'    => 'permissions',
        'model'    => Arcanedev\LaravelAuth\Models\Permission::class,
        'observer' => Arcanedev\LaravelAuth\Observers\PermissionObserver::class,
    ],

    /* ------------------------------------------------------------------------------------------------
     |  User Confirmation
     | ------------------------------------------------------------------------------------------------
     */
    'user-confirmation'  => [
        'enabled'   => false,
        'length'    => 30,
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Throttles
     | ------------------------------------------------------------------------------------------------
     */
    'throttles'          => [
        'enabled'   => true,
        'table'     => 'throttles',
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Seeds
     | ------------------------------------------------------------------------------------------------
     */
    'seeds'              => [
        'users' => [
            [
                'username'   => 'admin',
                'email'      => env('ADMIN_USER_EMAIL', 'admin@email.com'),
                'password'   => env('ADMIN_USER_PASSWORD', 'password'),
            ],
        ],
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Other Stuff
     | ------------------------------------------------------------------------------------------------
     */
    'use-observers'      => true,

    'slug-separator'     => '.',
];
