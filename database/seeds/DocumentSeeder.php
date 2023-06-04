<?php

use App\Document;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        copy('public/assets/factory/documents/1.jpg','storage/app/documents/1.jpg');
        copy('public/assets/factory/documents/2.jpg','storage/app/documents/2.jpg');
        copy('public/assets/factory/documents/3.jpg','storage/app/documents/3.jpg');
        copy('public/assets/factory/documents/4.jpg','storage/app/documents/4.jpg');

        Document::insert([
            [
                'name' => 'Test document 1',
                'file' => '1.jpg',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Test document 2',
                'file' => '2.jpg',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Test document 3',
                'file' => '3.jpg',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ],
            [
                'name' => 'Test document 4',
                'file' => '4.jpg',
                'user_id' => 1,
                'created_at' => now(),
                'updated_at' => now()
            ]
        ]);
    }
}
