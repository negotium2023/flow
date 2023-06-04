<?php

use Faker\Generator as Faker;

$factory->define(App\Division::class, function (Faker $faker) {
    return [
        'name' => env('APP_NAME').": " . $faker->country
    ];
});
