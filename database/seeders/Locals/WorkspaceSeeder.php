<?php

namespace Database\Seeders\Locals;

use App\Models\Workspace;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class WorkspaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Workspace::create([
            'name' => 'Personal'
        ]);
        Workspace::create([
            'name' => 'Side Job'
        ]);
    }
}
