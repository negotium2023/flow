<?php

use Faker\Generator as Faker;

$factory->define(App\Region::class, function (Faker $faker) {
    return [
        'name' => $faker->state,
        'division_id' => $faker->numberBetween(1,3)
    ];
});
