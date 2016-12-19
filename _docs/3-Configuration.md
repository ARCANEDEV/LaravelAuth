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
        'observer' => Arcanedev\LaravelAuth\Models\Observers\UserObserver::class,
    ],
    
    'roles'              => [
        'table'    => 'roles',
        'model'    => Arcanedev\LaravelAuth\Models\Role::class,
        'observer' => Arcanedev\LaravelAuth\Models\Observers\RoleObserver::class,
    ],
    
    'permissions-groups' => [
        'table'    => 'permissions_groups',
        'model'    => Arcanedev\LaravelAuth\Models\PermissionsGroup::class,
        'observer' => Arcanedev\LaravelAuth\Models\Observers\PermissionsGroupObserver::class,
    ],
    
    'permissions'        => [
        'table'    => 'permissions',
        'model'    => Arcanedev\LaravelAuth\Models\Permission::class,
        'observer' => Arcanedev\LaravelAuth\Models\Observers\PermissionObserver::class,
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

## User Impersonation

You can enable the user impersonation feature by modifying the `enable` value and also you can change the session `key` used to store the impersonated user id.

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  User Impersonation
     | ------------------------------------------------------------------------------------------------
     */
    'impersonation' => [
        'enabled' => false,
        'key'     => 'impersonate',
    ],
    
    // ...
];
```

## User Last Activity 

You can enable or disable the user `last activity` tracking by modifying the `enable` value and also you can set the minutes value as a minimum online duration.

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  User Last Activity
     | ------------------------------------------------------------------------------------------------
     */
    'track-activity' => [
        'enabled' => true,

        'minutes' => 5,
    ],
    
    // ...
];
```

## Socialite

You can enable the socialite support by modifying the `enable` value.

You can also manage any supported `drivers` by enable/disable it.

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  User Impersonation
     | ------------------------------------------------------------------------------------------------
     */
    'socialite' => [
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

You can `enable` or `disable` the model observers by editing the `use-observers` value.  

```php
<?php

return [
    // ...
    
    /* ------------------------------------------------------------------------------------------------
     |  Other Stuff
     | ------------------------------------------------------------------------------------------------
     */
    'use-observers'      => true,
];
```
