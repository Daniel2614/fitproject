<?php

use Faker\Generator as Faker;
use App\VitalGym\Entities\Customer;
use App\VitalGym\Entities\Attendance;

$factory->define(Attendance::class, function (Faker $faker) {
    return [
        'date' => $faker->dateTime,
        'customer_id' => function () {
            return factory(Customer::class)->create()->id;
        },
    ];
});
