<?php

use Faker\Generator as Faker;
use App\VitalGym\Entities\User;
use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\Routine;
use App\VitalGym\Entities\Customer;

$factory->define(Customer::class, function (Faker $faker) {
    return [
        'ci' => null,
        'birthdate' => $faker->date(),
        'gender' => $faker->randomElement(['male', 'feminine']),
        'medical_observations' => $faker->sentence,
        'level_id' => function () {
            return factory(Level::class)->create()->id;
        },
        'routine_id' => function () {
            return factory(Routine::class)->create()->id;
        },
        'user_id' => function () {
            return factory(User::class)->states('customer')->create()->id;
        },
    ];
});

$factory->state(Customer::class, 'active', [
    'user_id' => function () {
        return factory(User::class)->states('customer', 'active')->create()->id;
    },
]);
