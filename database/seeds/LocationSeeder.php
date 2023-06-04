<?php

use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('division_user')->insert([
            [
                'user_id' => 1,
                'division_id' => 1
            ],
            [
                'user_id' => 10,
                'division_id' => 1
            ],
            [
                'user_id' => 11,
                'division_id' => 1
            ],
            [
                'user_id' => 12,
                'division_id' => 1
            ]
        ]);

        DB::table('region_user')->insert([
            [
                'user_id' => 1,
                'region_id' => 1
            ],
            [
                'user_id' => 10,
                'region_id' => 1
            ],
            [
                'user_id' => 11,
                'region_id' => 1
            ],
            [
                'user_id' => 12,
                'region_id' => 1
            ]
        ]);

        DB::table('area_user')->insert([
            [
                'user_id' => 1,
                'area_id' => 1
            ],
            [
                'user_id' => 10,
                'area_id' => 1
            ],
            [
                'user_id' => 11,
                'area_id' => 1
            ],
            [
                'user_id' => 12,
                'area_id' => 1
            ]
        ]);

        DB::table('office_user')->insert([
            [
                'user_id' => 1,
                'office_id' => 1
            ],
            [
                'user_id' => 1,
                'office_id' => 2
            ],
            [
                'user_id' => 10,
                'office_id' => 1
            ],
            [
                'user_id' => 10,
                'office_id' => 2
            ],
            [
                'user_id' => 11,
                'office_id' => 1
            ],
            [
                'user_id' => 11,
                'office_id' => 2
            ],
            [
                'user_id' => 12,
                'office_id' => 1
            ],
            [
                'user_id' => 12,
                'office_id' => 2
            ]
        ]);
    }
}
