<?php

use Illuminate\Database\Seeder;
use App\Region;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Region::insert([
            [
                'id' => 1,
                'name' => 'Ireland',
                'division_id' => 1
            ],
            [
                'id' => 2,
                'name' => 'NI & UK',
                'division_id' => 1
            ]
        ]);
    }
}
