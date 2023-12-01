<?php

use App\Enums\AccountsType;
use App\Enums\TransactionsType;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountPivot;
use App\Models\Category;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Carbon\Carbon;
use Inertia\Testing\AssertableInertia as Assert;
use PrinsFrank\Standards\Currency\CurrencyAlpha3;

test('user can access account pages', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    $accountGroup = AccountGroup::factory(3)
        ->hasAttached(
            Account::factory(rand(1,10), ['type' => AccountsType::ASSETS->value]),
            [
                'workspace_id' => $workspace->id,
                'opening_date' => now()->format('d/m/Y'),
                'starting_balance' => rand(10,100),
                'currency' => CurrencyAlpha3::from('MYR')->value,
            ]
        )
        ->create([
            'type' => AccountsType::ASSETS->value
        ]);
    $workspace->accountGroups()->attach($accountGroup->pluck('id'));

    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('accounts.index'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Accounts/Index')
            ->has('accounts', fn (Assert $page) => $page
                ->has('assets', 3)
                ->has('liabilities', 0)
            )
        );
    $response->assertStatus(200);
    
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('accounts.create'))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Accounts/Form')
            ->has('account_group.assets', 3)
            ->has('account_group.liabilities', 0)
            ->where('types', AccountsType::dropdown())
            ->where('data', [
                'id' => '',
                'name' => '',
                'account_group' => '',
                'type' => '',
                'opening_date' => '',
                'starting_balance' => '0',
                'currency' => CurrencyAlpha3::from('MYR')->value,
                'notes' => '',
            ])
        );
    $response->assertStatus(200);
    
    $account = $accountGroup->first()->accounts()->first();
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->get(route('accounts.edit', ['account' => $account->id]))
        ->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard/Accounts/Form')
            ->has('account_group.assets', 3)
            ->has('account_group.liabilities', 0)
            ->where('edit_mode', true)
            ->where('types', AccountsType::dropdown())
            ->where('data', [
                'id' => $account->id,
                'name' => $account->name,
                'account_group' => $accountGroup->first()->id,
                'type' => $account->type,
                'opening_date' => $account->details->opening_date,
                'starting_balance' => $account->details->starting_balance,
                'currency' => $account->details->currency,
                'notes' => $account->details->notes,
            ])
        );
    $response->assertStatus(200);
});

test('user can perform store, update and destroy', function () {
    $user = User::factory()->create();
    $workspace = Workspace::factory()->create();
    $workspace->users()->attach($user->id);
    app(WorkspaceService::class)->change($workspace->id);
    $accountGroup1 = AccountGroup::factory()
        ->create([
            'type' => AccountsType::ASSETS->value
        ]);
    $accountGroup2 = AccountGroup::factory()
        ->create([
            'type' => AccountsType::ASSETS->value
        ]);
    $workspace->accountGroups()->sync([$accountGroup1->id, $accountGroup2->id]);

    $data = [
        'name' => 'testcreate'.rand(4,10),
        'account_group' => $accountGroup1->id,
        'type' => AccountsType::ASSETS->value,
        'opening_date' => now()->format('d/m/Y'),
        'starting_balance' => rand(10,100),
        'currency' => CurrencyAlpha3::from('MYR')->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->post(route('accounts.store'), $data);
    $response->assertRedirectToRoute('accounts.index');
    $this->assertDatabaseHas('account_pivot', collect($data)->merge([
        'opening_date' => Carbon::createFromFormat('d/m/Y', $data['opening_date'])->format('Y-m-d')
    ])->except([
        'name', 'account_group', 'type'
    ])->toArray());

    $account_pivot = AccountPivot::where('account_group_id', $data['account_group'])->where('starting_balance', $data['starting_balance'])->select('account_id')->first();
    $this->assertDatabaseHas('transactions', [
        'type' => TransactionsType::INCOME->value,
        'category_id' => Category::isPositiveOpeningBalance()->select('id')->first()->id,
        'account_id' => $account_pivot->account_id,
        'amount' => $data['starting_balance'],
    ]);
    
    $created_account = Account::where('name', $data['name'])->first();
    $this->assertModelExists($created_account);
    $data = [
        'name' => 'testupdate'.rand(4,10),
        'account_group' => $accountGroup2->id,
        'type' => AccountsType::LIABILITIES->value,
        'opening_date' => now()->format('d/m/Y'),
        'starting_balance' => rand(10,100),
        'currency' => CurrencyAlpha3::from('MYR')->value,
    ];
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->put(route('accounts.update', ['account' => $created_account->id]), $data);
    $updated_account = Account::where('name', $data['name'])->first();
    $this->assertModelExists($updated_account);
    $response->assertRedirectToRoute('accounts.index');
    $this->assertDatabaseHas('account_pivot', [
        'workspace_id' => $workspace->id,
        'account_id' => $updated_account->id,
        'account_group_id' => $data['account_group'],
    ]);
    
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->delete(route('accounts.destroy', ['account' => $updated_account->id]));
    $response->assertInternalServerError();

    Transaction::where('account_id', $updated_account->id)->delete();
    $response = $this->actingAs($user)
        ->withSession([WorkspaceService::KEY => $workspace->id])
        ->delete(route('accounts.destroy', ['account' => $updated_account->id]));
    $response->assertRedirectToRoute('accounts.index');
    $this->assertDatabaseMissing('account_pivot', [
        'account_id' => $updated_account->id,
        'account_group_id' => $data['account_group'],
    ]);
});