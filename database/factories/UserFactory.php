<?php

use App\VitalGym\Entities\User;
use App\VitalGym\Entities\ActivationToken;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

$factory->define(User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name'           => $faker->firstName,
        'last_name'      => $faker->lastName,
        'avatar'         => 'avatars/default-avatar.jpg',
        'email'          => $faker->unique()->safeEmail,
        'phone'          => $faker->phoneNumber,
        'cell_phone'     => $faker->phoneNumber,
        'address'         => $faker->address,
        'password'       => $password ?: $password = bcrypt('secret'),
        'active'         => $faker->randomElement([true, false, true]),
        'role'           => $faker->randomElement(['admin', 'customer', 'customer']),
        'remember_token' => str_random(10),
    ];
});

$factory->state(User::class, 'admin', [
    'role' => 'admin',
]);

$factory->state(User::class, 'customer', [
    'role' => 'customer',
]);

$factory->state(User::class, 'active', [
    'active' => true,
]);

$factory->define(ActivationToken::class, function (Faker\Generator $faker) {
    return [
        'token'   => str_random(128),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
    ];
});
