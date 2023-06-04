<?php

use Illuminate\Database\Seeder;
use App\Status;

class StatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Status::insert([
            [
                'name' => 'Active',
                'creator_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'InActive',
                'creator_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
