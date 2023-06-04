<?php

use Illuminate\Database\Seeder;
use App\Division;

class DivisionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Division::insert([
            [
                'id' => 1,
                'name' => 'UHY FDW Group'
            ]
        ]);
    }
}
