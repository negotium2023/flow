<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function __construct()
    {
        $this->storage_paths = [
            "storage/app/avatars",
            "storage/app/documents",
            "storage/app/templates",
        ];
    }

    public function run()
    {
        //clean storage directories
        if (env('APP_DEBUG', false)) {
            foreach ($this->storage_paths as $storage_path) {
                array_map('unlink', glob($storage_path . "/*"));
                if (!file_exists($storage_path)) {
                    mkdir($storage_path);
                }
            }
        }

        $this->call(RoleSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(RoleUserSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(PermissionRoleSeeder::class);
        $this->call(DivisionSeeder::class);
        $this->call(RegionSeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(OfficeSeeder::class);
        $this->call(LocationSeeder::class);
        $this->call(ReferrerSeeder::class);
        $this->call(DocumentSeeder::class);
        $this->call(TemplateSeeder::class);
        $this->call(ProcessSeeder::class);
        $this->call(ClientSeeder::class);
        $this->call(ConfigSeeder::class);
        $this->call(EmailSignatureSeeder::class);
        $this->call(EmailTemplateSeeder::class);
        $this->call(ReferrerTypeSeeder::class);
        $this->call(StatusSeeder::class);
        //$this->call(ProcessDataSeeder::class);
        //$this->call(InsightSeeder::class);
    }
}
