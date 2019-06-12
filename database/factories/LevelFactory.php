<?php

use Faker\Generator as Faker;
use App\VitalGym\Entities\Level;

$factory->define(Level::class, function (Faker $faker) {
    return [
        'name' => $faker->randomElement(['principiante', 'medio', 'experto']),
    ];
});
