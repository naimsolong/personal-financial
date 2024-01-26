<?php

namespace Database\Seeders\Locals;

use App\Models\Waitlist;
use Illuminate\Database\Seeder;

class WaitlistSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Waitlist::factory()->count(rand(10, 50))->create();
    }
}
