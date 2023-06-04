<?php

use Illuminate\Database\Seeder;
use App\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        copy('public/assets/default.png','storage/app/avatars/default.png');

        copy('public/assets/factory/avatars/1.png','storage/app/avatars/1.png');
        copy('public/assets/factory/avatars/2.png','storage/app/avatars/2.png');
        copy('public/assets/factory/avatars/3.png','storage/app/avatars/3.png');
        copy('public/assets/factory/avatars/4.png','storage/app/avatars/4.png');
        copy('public/assets/factory/avatars/5.png','storage/app/avatars/5.png');
        copy('public/assets/factory/avatars/6.png','storage/app/avatars/6.png');
        copy('public/assets/factory/avatars/7.png','storage/app/avatars/7.png');
        copy('public/assets/factory/avatars/8.png','storage/app/avatars/8.png');
        copy('public/assets/factory/avatars/9.png','storage/app/avatars/9.png');
        copy('public/assets/factory/avatars/10.png','storage/app/avatars/10.png');
        copy('public/assets/factory/avatars/11.png','storage/app/avatars/11.png');
        copy('public/assets/factory/avatars/12.png','storage/app/avatars/12.png');

        User::insert([
            [
                'id' => 1,
                'first_name' => 'Test',
                'last_name' => 'Admin',
                'email' => 'a@example.com',
                'password' => bcrypt('demoadmin'),
                'avatar' => '1.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 2,
                'first_name' => 'Test',
                'last_name' => 'Managing Director',
                'email' => 'md@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '2.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 3,
                'first_name' => 'Test',
                'last_name' => 'Director',
                'email' => 'd@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '3.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 4,
                'first_name' => 'Test',
                'last_name' => 'Marketing Manager',
                'email' => 'mm@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '4.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 5,
                'first_name' => 'Test',
                'last_name' => 'Business Development and Marketing Manager',
                'email' => 'bdmm@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '5.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 6,
                'first_name' => 'Test',
                'last_name' => 'Audit Manager responsible',
                'email' => 'amr@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '6.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 7,
                'first_name' => 'Test',
                'last_name' => 'Tax Manager responsible',
                'email' => 'tmr@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '7.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 8,
                'first_name' => 'Test',
                'last_name' => 'Co Sec Manager responsible',
                'email' => 'csmr@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '8.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 9,
                'first_name' => 'Test',
                'last_name' => 'Payroll Manager responsible',
                'email' => 'pmr@example.com',
                'password' => bcrypt('secret'),
                'avatar' => '9.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 10,
                'first_name' => 'Francois',
                'last_name' => 'van Heerden',
                'email' => 'francoisvanheerden@fdw.ie',
                'password' => bcrypt('secret'),
                'avatar' => '10.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 11,
                'first_name' => 'Nicola',
                'last_name' => 'Mernagh',
                'email' => 'nicolamernagh@fdw.ie',
                'password' => bcrypt('secret'),
                'avatar' => '11.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ],
            [
                'id' => 12,
                'first_name' => 'Nico',
                'last_name' => 'van der Meulen',
                'email' => 'nico@blackboardbs.com',
                'password' => bcrypt('secret'),
                'avatar' => '12.png',
                'verified' => '1',
                'remember_token' => str_random(10),
                'created_at' => Carbon::now()
            ]
        ]);
    }
}
