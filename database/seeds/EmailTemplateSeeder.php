<?php

use Illuminate\Database\Seeder;
use App\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        EmailTemplate::insert([
            ['id'=>'1','name' => 'Test template','email_content'=>'Dear Reader<br /><br /><strong>Please find attached.</strong>','user_id'=>'1','deleted_at'=>null,'created_at'=>now(),'updated_at'=>now()]
        ]);
    }
}
