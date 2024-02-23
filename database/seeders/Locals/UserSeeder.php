<?php

namespace Database\Seeders\Locals;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $workspaces_id = Workspace::pluck('id');

        $user = User::firstOrCreate([
            'email' => 'test@email.com',
        ],
            User::factory([
                'email' => 'test@email.com',
            ])->raw()
        );

        $user->workspaces()->sync($workspaces_id);
    }
}
