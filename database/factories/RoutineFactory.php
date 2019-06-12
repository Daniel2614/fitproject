<?php

use Faker\Generator as Faker;
use App\VitalGym\Entities\Level;
use App\VitalGym\Entities\Routine;

$factory->define(Routine::class, function (Faker $faker) {
    return [
        'level_id' => function () {
            return factory(Level::class)->create()->id;
        },
    ];
});
