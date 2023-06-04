<?php

use Faker\Generator as Faker;

$factory->define(\App\Insight::class, function (Faker $faker) {
    return [
        'name' => $faker->company,
        'revenue_change' => $this->faker->randomElement($array = [-1,1]) * $faker->biasedNumberBetween(20,0),
        'size' => $faker->numberBetween(1,3000)
    ];
});
