<?php

use App\Config;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Config::insert([
            'onboard_days' => 18,
            'onboards_per_day' => 2,
            'client_target_data' => 30,
            'client_converted' => 16,
            'client_conversion' => 14,
            'enable_support' => true,
            'support_email' => 'onboarding@blackboardbs.com',
        ]);
    }
}