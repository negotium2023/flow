<?php

use Faker\Generator as Faker;

$factory->define(App\Office::class, function (Faker $faker) {
    return [
        'name' => $faker->city,
        'area_id' => $faker->numberBetween(1,15)
    ];
});
