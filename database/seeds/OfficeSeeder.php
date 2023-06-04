<?php

use Illuminate\Database\Seeder;
use App\Office;

class OfficeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Office::insert([
            [
                'id' => 1,
                'name' => 'Dundalk',
                'area_id' => 1
            ],
            [
                'id' => 2,
                'name' => 'Balbriggan',
                'area_id' => 1
            ]
        ]);
    }
}
