<?php

use Illuminate\Database\Seeder;
use App\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::insert([
            [
                'id'=>1,
                'name'=>'admin',
                'display_name'=>'Administrator',
                'description'=>''
            ],
            [
                'id'=>2,
                'name'=>'managing_director',
                'display_name'=>'Managing Director',
                'description'=>''
            ],
            [
                'id'=>3,
                'name'=>'director',
                'display_name'=>'Director',
                'description'=>''
            ],
            [
                'id'=>4,
                'name'=>'marketing_manager',
                'display_name'=>'Marketing Manager',
                'description'=>''
            ],
            [
                'id'=>5,
                'name'=>'business_development_and_marketing_manager',
                'display_name'=>'Business Development and Marketing Manager',
                'description'=>''
            ],
            [
                'id'=>6,
                'name'=>'audit_manager_responsible',
                'display_name'=>'Audit Manager responsible',
                'description'=>''
            ],
            [
                'id'=>7,
                'name'=>'tax_manager_responsible',
                'display_name'=>'Tax Manager responsible',
                'description'=>''
            ],
            [
                'id'=>8,
                'name'=>'co_sec_manager_responsible',
                'display_name'=>'Co Sec Manager responsible',
                'description'=>''
            ],
            [
                'id'=>9,
                'name'=>'payroll_manager_responsible',
                'display_name'=>'Payroll Manager responsible',
                'description'=>''
            ]
        ]);
    }
}
