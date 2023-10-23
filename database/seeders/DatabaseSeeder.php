<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    protected const LOCAL_SEEDER = [
        UserSeeder::class,
        CategorySeeder::class,
        AccountSeeder::class,
        WorkspaceSeeder::class,
        TransactionSeeder::class,
    ];
    
    protected const TESTING_SEEDER = [];
    
    protected const PRODUCTION_SEEDER = [];

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(InitSeeder::class);
        
        $this->call(match(app()->environment()) {
            'local' => self::LOCAL_SEEDER,
            'testing' => self::TESTING_SEEDER,
            'production' => self::PRODUCTION_SEEDER,
        });
    }
}
