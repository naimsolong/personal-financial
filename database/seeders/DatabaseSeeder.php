<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Database\Seeders\Locals as LocalSeeders;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected const LOCAL_SEEDER = [
        InitSeeder::class,
        LocalSeeders\WorkspaceSeeder::class,
        LocalSeeders\UserSeeder::class,
        LocalSeeders\CategorySeeder::class,
        LocalSeeders\AccountSeeder::class,
        LocalSeeders\TransactionSeeder::class,
    ];

    protected const TESTING_SEEDER = [
        InitSeeder::class,
    ];

    protected const PRODUCTION_SEEDER = [
        InitSeeder::class,
    ];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(match (app()->environment()) {
            'local' => self::LOCAL_SEEDER,
            'testing' => self::TESTING_SEEDER,
            'production' => self::PRODUCTION_SEEDER,
        });
    }
}
