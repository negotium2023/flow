<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BusinessUnitsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('business_units')->insert(['name' => 'RBB']);
        DB::table('business_units')->insert(['name' => 'EveryDay Banking']);
        DB::table('business_units')->insert(['name' => 'Relationship Banking']);
        DB::table('business_units')->insert(['name' => 'CIB']);
        DB::table('business_units')->insert(['name' => 'No Exposure']);
        DB::table('business_units')->insert(['name' => 'Vehicle Finance']);
        DB::table('business_units')->insert(['name' => 'Wealth']);
    }
}
