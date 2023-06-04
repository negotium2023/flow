<?php

use Faker\Generator as Faker;

$factory->define(\App\Client::class, function (Faker $faker) {

    $first_name = (($faker->boolean) ? $faker->firstName : null);
    $last_name = (($faker->boolean) ? $faker->lastName : null);
    $company = $faker->company;

    //$created = Carbon\Carbon::createFromTimeStamp($faker->dateTimeThisMonth()->getTimestamp())->subDays(20);

    return [
        'first_name' => $first_name,
        'last_name' => $last_name,
        'company' => $company,
        'email' => $faker->safeEmail,
        'contact' => $faker->phoneNumber,
        'introducer_id' => 1,
        'office_id' => $faker->numberBetween(1, 2),
        'process_id' => 1,
        'created_at' => now(),
        'updated_at' => now(),
        //'completed_at' => (($faker->boolean()) ? $created->copy()->addDays($faker->numberBetween(1, 20)) : null)
    ];
});
