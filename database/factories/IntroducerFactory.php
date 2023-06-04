<?php

use Faker\Generator as Faker;

$factory->define(App\Referrer::class, function (Faker $faker) {
    return [
        'first_name' => $faker->firstName,
        'last_name' => $faker->firstName,
        'email' => $faker->safeEmail,
        'contact' => $faker->phoneNumber,
        'uhy_referral' => 0,
        'uhy_firm_name' => $faker->firstName,
        'uhy_contact' => $faker->phoneNumber
    ];
});
