<?php

return [
    /* ------------------------------------------------------------------------------------------------
     |  Database
     | ------------------------------------------------------------------------------------------------
     */
    'database'           => [
        'connection' => config('database.default'),
        'prefix'     => 'auth_'
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Models
     | ------------------------------------------------------------------------------------------------
     */
    'users'              => [
        'table'          => 'users',
        'model'          => \Arcanedev\LaravelAuth\Models\User::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\UserObserver::class,
        'slug-separator' => '.',
    ],

    'roles'              => [
        'table'          => 'roles',
        'model'          => \Arcanedev\LaravelAuth\Models\Role::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\RoleObserver::class,
        'slug-separator' => '-',
    ],

    'role-user'          => [
        'table' => 'role_user',
        'model' => \Arcanedev\LaravelAuth\Models\Pivots\RoleUser::class,
    ],

    'permissions-groups' => [
        'table'          => 'permissions_groups',
        'model'          => \Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\PermissionsGroupObserver::class,
        'slug-separator' => '-',
    ],

    'permissions'        => [
        'table'          => 'permissions',
        'model'          => \Arcanedev\LaravelAuth\Models\Permission::class,
        'observer'       => \Arcanedev\LaravelAuth\Models\Observers\PermissionObserver::class,
        'slug-separator' => '.',
    ],

    'permission-role'    => [
        'table' => 'permission_role',
        'model' => \Arcanedev\LaravelAuth\Models\Pivots\PermissionRole::class,
    ],

    'password-resets' => [
        'table' => 'password_resets',
        'model' => \Arcanedev\LaravelAuth\Models\PasswordReset::class,
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Observers
     | ------------------------------------------------------------------------------------------------
     */
    'observers'      => [
        'enabled'  => true,

        'bindings' => [
            'users'              => \Arcanesoft\Contracts\Auth\Models\User::class,
            'roles'              => \Arcanesoft\Contracts\Auth\Models\Role::class,
            'permissions-groups' => \Arcanesoft\Contracts\Auth\Models\PermissionsGroup::class,
            'permissions'        => \Arcanesoft\Contracts\Auth\Models\Permission::class,
        ],
    ],

    /* ------------------------------------------------------------------------------------------------
     |  User Confirmation
     | ------------------------------------------------------------------------------------------------
     */
    'user-confirmation'  => [
        'enabled' => false,

        'length'  => 30,
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
     |  User Last Activity
     | ------------------------------------------------------------------------------------------------
     */
    'track-activity' => [
        'enabled' => true,

        'minutes' => 5,
    ],

    /* ------------------------------------------------------------------------------------------------
     |  Socialite
     | ------------------------------------------------------------------------------------------------
     */
    'socialite'          => [
        'enabled' => false,

        'drivers' => [
            'bitbucket' => [
                'enabled' => false,
            ],

            'facebook'  => [
                'enabled' => true,
            ],

            'github'    => [
                'enabled' => false,
            ],

            'google'    => [
                'enabled' => true,
            ],

            'linkedin'  => [
                'enabled' => false,
            ],

            'twitter'   => [
                'enabled' => true,
            ],
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

];
