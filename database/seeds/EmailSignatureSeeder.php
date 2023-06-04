<?php

use Illuminate\Database\Seeder;
use App\EmailSignature;

class EmailSignatureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailSignature::insert([
            ['id'=>'1','name' => 'Test Signature','template_content'=>'Kind Regards<br /><strong>UHY</strong>','user_id'=>'1','deleted_at'=>null,'created_at'=>now(),'updated_at'=>now()]
        ]);
    }
}
