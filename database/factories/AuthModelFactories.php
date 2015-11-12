<?php

use Arcanedev\LaravelAuth\Models\User;
use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* ------------------------------------------------------------------------------------------------
 |  Users Factory
 | ------------------------------------------------------------------------------------------------
 */
/** @var Factory $factory */
$factory->define(User::class, function (Faker $faker) {
    return [
        'username'       => $faker->userName,
        'first_name'     => $faker->firstName,
        'last_name'      => $faker->lastName,
        'email'          => $faker->email,
        'password'       => bcrypt(str_random(10)),
        'remember_token' => str_random(10),
        'is_active'      => $faker->boolean(75),
    ];
});

$factory->defineAs(User::class, 'admin', function (Faker $faker) use ($factory) {
    unset($faker);

    $user = $factory->raw(User::class);

    return array_merge($user, [
        'is_active' => true,
        'is_admin'  => true,
    ]);
});
