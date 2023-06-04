<?php

use Faker\Generator as Faker;

$factory->define(App\Area::class, function (Faker $faker) {
    return [
        'name' => $faker->streetName,
        'region_id' => $faker->numberBetween(1,8)
    ];
});
