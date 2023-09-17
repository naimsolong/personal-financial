<?php

namespace Database\Seeders;

use App\Models\AccountGroup;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AccountGroup::factory(rand(5,15))
            ->create();
    }
}
