<?php

use Faker\Generator as Faker;

$factory->define(App\Template::class, function (Faker $faker) {
    return [
        'name' => 'Temp: '.$faker->colorName,
        'file' => $faker->file('public/assets/factory/templates', 'storage/app/templates', false),
        'user_id' => $faker->numberBetween(1, 9)
    ];
});
