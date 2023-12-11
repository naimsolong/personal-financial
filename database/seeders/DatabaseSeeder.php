<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Locals as LocalSeeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected const LOCAL_SEEDER = [
        LocalSeeders\WorkspaceSeeder::class,
        InitSeeder::class,
        LocalSeeders\UserSeeder::class,
        LocalSeeders\CategorySeeder::class,
        LocalSeeders\AccountSeeder::class,
        LocalSeeders\TransactionSeeder::class,
    ];

    protected const TESTING_SEEDER = [];

    protected const PRODUCTION_SEEDER = [];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(match (app()->environment()) {
            'local', 'testing' => self::LOCAL_SEEDER,
            'production' => self::PRODUCTION_SEEDER,
        });
    }
}
