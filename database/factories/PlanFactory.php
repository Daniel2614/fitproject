<?php

use Faker\Generator as Faker;
use App\VitalGym\Entities\Plan;

$factory->define(Plan::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['mensual', 'anual', 'semestral']),
        'price' => $faker->numberBetween(2000, 30000),
        'is_premium' => false,
    ];
});

$factory->state(Plan::class, 'premium', ['is_premium' =>true]);
