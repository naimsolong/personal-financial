<?php

use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\Category;
use App\Models\CategoryGroup;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\DB;

test('model class has correct properties', function () {
    expect(app(Workspace::class)->getFillable())->toBeArray()->toBe([
        'name',
        'slug',
    ]);
});

test('model able to perform CRUD', function () {
    $workspace = Workspace::factory()->create();
    $this->assertModelExists($workspace);

    $name = 'test this';
    $workspace->update([
        'name' => $name,
    ]);
    $workspace->refresh();
    $this->assertDatabaseHas('workspaces', [
        'id' => $workspace->id,
        'name' => $name,
        'slug' => $workspace->slug,
    ]);

    $workspace->delete();
    $this->assertModelMissing($workspace);
});

test('able to attach, detach and sync', function () {
    // Prepare data
    $user = User::factory()->create();
    $categoryGroup = CategoryGroup::factory(rand(5, 10))->create();
    $accountGroup = AccountGroup::factory(rand(5, 10))->create();
    $workspace = Workspace::factory()->create();

    // Test for category groups
    $id = $categoryGroup->first()->id;
    $workspace->categoryGroups()->attach($id);
    $this->assertDatabaseHas('workspace_categories', [
        'workspace_id' => $workspace->id,
        'category_group_id' => $id,
    ]);

    $workspace->categoryGroups()->detach($id);
    $this->assertDatabaseMissing('workspace_categories', [
        'workspace_id' => $workspace->id,
        'category_group_id' => $id,
    ]);

    $categoryGroupIds = $categoryGroup->pluck('id');
    $workspace->categoryGroups()->sync($categoryGroupIds);
    expect(
        DB::table('workspace_categories')->where('workspace_id', $workspace->id)->whereIn('category_group_id', $categoryGroupIds)->count()
    )->toBe($categoryGroupIds->count());

    $workspace->categoryGroups()->detach();
    expect(
        DB::table('workspace_categories')->where('workspace_id', $workspace->id)->count()
    )->toBe(0);

    // Test for account groups
    $id = $accountGroup->first()->id;
    $workspace->accountGroups()->attach($id);
    $this->assertDatabaseHas('workspace_accounts', [
        'workspace_id' => $workspace->id,
        'account_group_id' => $id,
    ]);

    $workspace->accountGroups()->detach($id);
    $this->assertDatabaseMissing('workspace_accounts', [
        'workspace_id' => $workspace->id,
        'account_group_id' => $id,
    ]);

    $accountGroupIds = $accountGroup->pluck('id');
    $workspace->accountGroups()->sync($accountGroupIds);
    expect(
        DB::table('workspace_accounts')->where('workspace_id', $workspace->id)->whereIn('account_group_id', $accountGroupIds)->count()
    )->toBe($accountGroupIds->count());

    $workspace->accountGroups()->detach();
    expect(
        DB::table('workspace_accounts')->where('workspace_id', $workspace->id)->count()
    )->toBe(0);

    // Test for user
    $id = $user->id;
    $workspace->users()->attach($id);
    $this->assertDatabaseHas('workspace_users', [
        'workspace_id' => $workspace->id,
        'user_id' => $id,
    ]);

    $workspace->users()->detach($id);
    $this->assertDatabaseMissing('workspace_users', [
        'workspace_id' => $workspace->id,
        'user_id' => $id,
    ]);
});
