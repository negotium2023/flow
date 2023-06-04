<?php

use App\Template;
use Illuminate\Database\Seeder;

class TemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        copy('public/assets/factory/templates/About Us.pdf', 'storage/app/templates/About Us.pdf');
        copy('public/assets/factory/templates/Sample welcome letter.docx', 'storage/app/templates/Sample welcome letter.docx');
        copy('public/assets/factory/templates/Test Template v0.1.pdf', 'storage/app/templates/Test Template v0.1.pdf');

        Template::insert([
            [
                'name' => 'About us',
                'file' => 'About Us.pdf',
                'user_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Sample welcome letter',
                'file' => 'Sample welcome letter.docx',
                'user_id' => 1,
                'created_at' => now()
            ],
            [
                'name' => 'Test Template v0.1',
                'file' => 'Test Template v0.1.pdf',
                'user_id' => 1,
                'created_at' => now()
            ]
        ]);
    }
}
