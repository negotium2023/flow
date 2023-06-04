<?php

use Illuminate\Database\Seeder;

class ReferrerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Referrer::class, 25)->create();
    }
}
