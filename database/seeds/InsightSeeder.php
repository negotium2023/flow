<?php

use Illuminate\Database\Seeder;

class InsightSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        factory(App\Insight::class, 500)->create();
    }
}
