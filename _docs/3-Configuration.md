# 3. Configuration

## Database

This your database `connection` for your auth tables, you can customize it if want to use another database for auth instead of your app database.  

```php
<?php

return [
    /* ------------------------------------------------------------------------------------------------
     |  Database
     | ------------------------------------------------------------------------------------------------
     */
    'database'           => [
        'connection' => config('database.default'),
    ],
    
    //...
];
```

## Models

These are the auth Models. You can edit the table name, Model Class and also the Model Observer. 

```php
<?php

return [
    // ...
    
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
    
    // ...
];
```

## User Confirmation

You can enable the user confirmation feature by modifying the `enable` value and also you can change the `length` of the generated confirmation code.

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  User Confirmation
     | ------------------------------------------------------------------------------------------------
     */
    'user-confirmation'  => [
        'enabled'   => false,
        'length'    => 30,
    ],
    
    // ...
];
```

## Throttles

** WORK IN PROGRESS **

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  Throttles
     | ------------------------------------------------------------------------------------------------
     */
    'throttles'          => [
        'enabled'   => true,
        'table'     => 'throttles',
    ],
    
    // ...
];
```

## Seeds

You can specify the data to seed to your auth tables, for the time being, only the users seed is available. 

> Note: The users in seeder are **admins**.

```php
<?php

return [
    // ...
    
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
    
    // ...
];
```

## Other

You can `enable` or `disable` the model observers by editing the `use-observers` value and for the `slugs-eparator` is used for attribute like role or permission slugs.  

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  Other Stuff
     | ------------------------------------------------------------------------------------------------
     */
    'use-observers'      => true,
    'slug-separator'     => '.',
];
```
