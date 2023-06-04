<?php

use Illuminate\Database\Seeder;
use App\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Permission::insert([
            [
                'id'=>1,
                'name'=>'admin',
                'display_name'=>'Admin',
                'description'=>''
            ],
            [
                'id'=>2,
                'name'=>'maintain_client',
                'display_name'=>'Maintain Clients',
                'description'=>''
            ],
            [
                'id'=>3,
                'name'=>'maintain_referrer',
                'display_name'=>'Maintain Referrer',
                'description'=>''
            ],
            [
                'id'=>4,
                'name'=>'maintain_document',
                'display_name'=>'Maintain Documents',
                'description'=>''
            ],
            [
                'id'=>5,
                'name'=>'maintain_template',
                'display_name'=>'Maintain Templates',
                'description'=>''
            ],
            [
                'id'=>6,
                'name'=>'reports',
                'display_name'=>'View Reports',
                'description'=>''
            ]
        ]);
    }
}
