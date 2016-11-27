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
        'table'          => 'users',
        'model'          => Arcanedev\LaravelAuth\Models\User::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\UserObserver::class,
        'slug-separator' => '.',
    ],

    'roles'              => [
        'table'          => 'roles',
        'model'          => Arcanedev\LaravelAuth\Models\Role::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\RoleObserver::class,
        'slug-separator' => '-',
    ],

    'permissions-groups' => [
        'table'          => 'permissions_groups',
        'model'          => Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\PermissionsGroupObserver::class,
        'slug-separator' => '-',
    ],

    'permissions'        => [
        'table'          => 'permissions',
        'model'          => Arcanedev\LaravelAuth\Models\Permission::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\PermissionObserver::class,
        'slug-separator' => '.',
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
     |  User Impersonation
     | ------------------------------------------------------------------------------------------------
     */
    'impersonation'      => [
        'enabled' => false,
        'key'     => 'impersonate',
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Socialite
     | ------------------------------------------------------------------------------------------------
     */
    'socialite'          => [
        'enabled' => false,
        'drivers' => [
            'bitbucket' => false,
            'facebook'  => true,
            'github'    => false,
            'google'    => true,
            'linkedin'  => false,
            'twitter'   => true,
        ],
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
];
