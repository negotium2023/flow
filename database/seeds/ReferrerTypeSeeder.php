<?php

use Illuminate\Database\Seeder;
use App\ReferrerType;

class ReferrerTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        ReferrerType::insert([
            ["id"=>"1","description"=>"Individual","created_at"=>now(),"updated_at"=>now()],
            ["id"=>"2","description"=>"Website","created_at"=>now(),"updated_at"=>now()],
            ["id"=>"3","description"=>"Call","created_at"=>now(),"updated_at"=>now()],
            ["id"=>"4","description"=>"Company","created_at"=>now(),"updated_at"=>now()],
        ]);
    }
}
