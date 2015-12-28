<?php

use Faker\Generator as Faker;
use Illuminate\Database\Eloquent\Factory;

/* ------------------------------------------------------------------------------------------------
 |  Users Factory
 | ------------------------------------------------------------------------------------------------
 */
$userModel = config('laravel-auth.users.model');

/** @var Factory $factory */
$factory->define($userModel, function (Faker $faker) {
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

$factory->defineAs($userModel, 'admin', function () use ($factory, $userModel) {
    $user = $factory->raw($userModel);

    return array_merge($user, [
        'is_active' => true,
        'is_admin'  => true,
    ]);
});
