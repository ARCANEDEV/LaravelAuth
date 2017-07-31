<?php

return [

    /* -----------------------------------------------------------------
     |  Database
     | -----------------------------------------------------------------
     */

    'database'           => [
        'connection' => config('database.default'),
        'prefix'     => 'auth_'
    ],

    /* -----------------------------------------------------------------
     |  Models
     | -----------------------------------------------------------------
     */

    'users'              => [
        'table'          => 'users',
        'model'          => \Arcanedev\LaravelAuth\Models\User::class,
        'slug-separator' => '.',
    ],

    'roles'              => [
        'table'          => 'roles',
        'model'          => \Arcanedev\LaravelAuth\Models\Role::class,
        'slug-separator' => '-',
    ],

    'role-user'          => [
        'table' => 'role_user',
        'model' => \Arcanedev\LaravelAuth\Models\Pivots\RoleUser::class,
    ],

    'permissions-groups' => [
        'table'          => 'permissions_groups',
        'model'          => \Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
        'slug-separator' => '-',
    ],

    'permissions'        => [
        'table'          => 'permissions',
        'model'          => \Arcanedev\LaravelAuth\Models\Permission::class,
        'slug-separator' => '.',
    ],

    'permission-role'    => [
        'table' => 'permission_role',
        'model' => \Arcanedev\LaravelAuth\Models\Pivots\PermissionRole::class,
    ],

    'password-resets' => [
        'model' => \Arcanedev\LaravelAuth\Models\PasswordReset::class,
    ],

    /* -----------------------------------------------------------------
     |  Observers
     | -----------------------------------------------------------------
     */

    'events'      => [
        'enabled'  => true,

        'listeners' => [
            // User Model events & listeners
            //-----------------------------------------------------
            // Eloquent events
            Arcanedev\LaravelAuth\Events\Users\CreatingUser::class           => [
                Arcanedev\LaravelAuth\Listeners\Users\GenerateConfirmationCode::class,
            ],
            Arcanedev\LaravelAuth\Events\Users\CreatedUser::class            => [],
            Arcanedev\LaravelAuth\Events\Users\UpdatingUser::class           => [],
            Arcanedev\LaravelAuth\Events\Users\UpdatedUser::class            => [],
            Arcanedev\LaravelAuth\Events\Users\SavingUser::class             => [],
            Arcanedev\LaravelAuth\Events\Users\SavedUser::class              => [],
            Arcanedev\LaravelAuth\Events\Users\DeletingUser::class           => [
                Arcanedev\LaravelAuth\Listeners\Users\DetachingRoles::class,
            ],
            Arcanedev\LaravelAuth\Events\Users\DeletedUser::class            => [],
            Arcanedev\LaravelAuth\Events\Users\RestoringUser::class          => [],
            Arcanedev\LaravelAuth\Events\Users\RestoredUser::class           => [],
            // Custom events
            Arcanedev\LaravelAuth\Events\Users\ActivatingUser::class         => [],
            Arcanedev\LaravelAuth\Events\Users\ActivatedUser::class          => [],
            Arcanedev\LaravelAuth\Events\Users\DeactivatingUser::class       => [],
            Arcanedev\LaravelAuth\Events\Users\DeactivatedUser::class        => [],
            Arcanedev\LaravelAuth\Events\Users\AttachingRoleToUser::class    => [],
            Arcanedev\LaravelAuth\Events\Users\AttachedRoleToUser::class     => [],
            Arcanedev\LaravelAuth\Events\Users\SyncingUserWithRoles::class   => [],
            Arcanedev\LaravelAuth\Events\Users\SyncedUserWithRoles::class    => [],
            Arcanedev\LaravelAuth\Events\Users\DetachingRoleFromUser::class  => [],
            Arcanedev\LaravelAuth\Events\Users\DetachedRoleFromUser::class   => [],
            Arcanedev\LaravelAuth\Events\Users\DetachingRolesFromUser::class => [],
            Arcanedev\LaravelAuth\Events\Users\DetachedRolesFromUser::class  => [],

            // Role Model events & listeners
            //-----------------------------------------------------
            Arcanedev\LaravelAuth\Events\Roles\CreatingRole::class => [],
            Arcanedev\LaravelAuth\Events\Roles\CreatedRole::class  => [],
            Arcanedev\LaravelAuth\Events\Roles\UpdatingRole::class => [],
            Arcanedev\LaravelAuth\Events\Roles\UpdatedRole::class  => [],
            Arcanedev\LaravelAuth\Events\Roles\SavingRole::class   => [],
            Arcanedev\LaravelAuth\Events\Roles\SavedRole::class    => [],
            Arcanedev\LaravelAuth\Events\Roles\DeletingRole::class => [
                Arcanedev\LaravelAuth\Listeners\Roles\DetachingUsers::class,
                Arcanedev\LaravelAuth\Listeners\Roles\DetachingPermissions::class,
            ],
            Arcanedev\LaravelAuth\Events\Roles\DeletedRole::class  => [],
            // Custom
            Arcanedev\LaravelAuth\Events\Roles\AttachingUserToRole::class             => [],
            Arcanedev\LaravelAuth\Events\Roles\AttachedUserToRole::class              => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachingUserFromRole::class           => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachedUserFromRole::class            => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachingAllUsersFromRole::class       => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachedAllUsersFromRole::class        => [],
            Arcanedev\LaravelAuth\Events\Roles\AttachingPermissionToRole::class       => [],
            Arcanedev\LaravelAuth\Events\Roles\AttachedPermissionToRole::class        => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachingPermissionFromRole::class     => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachedPermissionFromRole::class      => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachingAllPermissionsFromRole::class => [],
            Arcanedev\LaravelAuth\Events\Roles\DetachedAllPermissionsFromRole::class  => [],

            // Permission Model events & listeners
            //-----------------------------------------------------
            Arcanedev\LaravelAuth\Events\Permissions\CreatingPermission::class => [],
            Arcanedev\LaravelAuth\Events\Permissions\CreatedPermission::class  => [],
            Arcanedev\LaravelAuth\Events\Permissions\UpdatingPermission::class => [],
            Arcanedev\LaravelAuth\Events\Permissions\UpdatedPermission::class  => [],
            Arcanedev\LaravelAuth\Events\Permissions\SavingPermission::class   => [],
            Arcanedev\LaravelAuth\Events\Permissions\SavedPermission::class    => [],
            Arcanedev\LaravelAuth\Events\Permissions\DeletingPermission::class => [
                Arcanedev\LaravelAuth\Listeners\Permissions\DetachingRoles::class,
            ],
            Arcanedev\LaravelAuth\Events\Permissions\DeletedPermission::class  => [],
            // Custom
            Arcanedev\LaravelAuth\Events\Permissions\AttachingRoleToPermission::class       => [],
            Arcanedev\LaravelAuth\Events\Permissions\AttachedRoleToPermission::class        => [],
            Arcanedev\LaravelAuth\Events\Permissions\DetachingRoleFromPermission::class     => [],
            Arcanedev\LaravelAuth\Events\Permissions\DetachedRoleFromPermission::class      => [],
            Arcanedev\LaravelAuth\Events\Permissions\DetachingAllRolesFromPermission::class => [],
            Arcanedev\LaravelAuth\Events\Permissions\DetachedAllRolesFromPermission::class  => [],
            Arcanedev\LaravelAuth\Events\Permissions\SyncingRolesWithPermission::class      => [],
            Arcanedev\LaravelAuth\Events\Permissions\SyncedRolesWithPermission::class       => [],

            // Permission's Group Model events & listeners
            //-----------------------------------------------------
            Arcanedev\LaravelAuth\Events\PermissionsGroups\CreatingPermissionsGroup::class => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\CreatedPermissionsGroup::class  => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\UpdatingPermissionsGroup::class => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\UpdatedPermissionsGroup::class  => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\SavingPermissionsGroup::class   => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\SavedPermissionsGroup::class    => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DeletingPermissionsGroup::class => [
                Arcanedev\LaravelAuth\Listeners\PermissionGroups\DetachingPermissions::class,
            ],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DeletedPermissionsGroup::class  => [],
            // Custom
            Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachingPermissionToGroup::class    => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachedPermissionToGroup::class     => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachingPermissionsToGroup::class   => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\AttachedPermissionsToGroup::class    => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachingPermissionFromGroup::class  => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachedPermissionFromGroup::class   => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachingPermissionsFromGroup::class => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachedPermissionsFromGroup::class  => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachingAllPermissions::class       => [],
            Arcanedev\LaravelAuth\Events\PermissionsGroups\DetachedAllPermissions::class        => [],
        ],
    ],

    /* -----------------------------------------------------------------
     |  User Confirmation
     | -----------------------------------------------------------------
     */

    'user-confirmation'  => [
        'enabled' => false,

        'length'  => 30,
    ],

    /* -----------------------------------------------------------------
     |  User Last Activity
     | -----------------------------------------------------------------
     */

    'track-activity' => [
        'enabled' => true,

        'minutes' => 5,
    ],

    /* -----------------------------------------------------------------
     |  Socialite
     | -----------------------------------------------------------------
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

    /* -----------------------------------------------------------------
     |  Throttles
     | -----------------------------------------------------------------
     */

    'throttles'          => [
        'enabled'   => true,

        'table'     => 'throttles',
    ],

    /* -----------------------------------------------------------------
     |  Seeds
     | -----------------------------------------------------------------
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
