<?php

use Illuminate\Database\Seeder;
use App\Area;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Area::insert([
            [
                'id' => 1,
                'name' => 'UHY Farrelly Dawe White Limited',
                'region_id' => 1
            ]
        ]);
    }
}
