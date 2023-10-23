<?php

namespace Database\Seeders;

use App\Models\AccountGroup;
use App\Models\CategoryGroup;
use App\Models\Transaction;
use App\Models\User;
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
        $workspace = Workspace::create([
            'name' => 'Personal',
        ]);

        $categoryGroupIds = CategoryGroup::forUser()->pluck('id');
        $workspace->categoryGroups()->sync($categoryGroupIds);

        $accountGroupIds = AccountGroup::pluck('id');
        $workspace->accountGroups()->sync($accountGroupIds);
        
        $workspace->users()->attach(User::first()->id);
        
        $workspace = Workspace::create([
            'name' => 'Side Job',
        ]);
        
        $workspace->users()->attach(User::first()->id);
    }
}
