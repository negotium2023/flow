<?php

use Faker\Generator as Faker;

$factory->define(App\Document::class, function (Faker $faker) {
    return [
        'name' => 'Doc: '.$faker->colorName,
        'file' => $faker->file('public/assets/factory/documents', 'storage/app/documents', false),
        'user_id' => $faker->numberBetween(1, 9)
    ];
});
