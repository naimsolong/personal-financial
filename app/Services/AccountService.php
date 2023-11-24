<?php

namespace App\Services;

use App\Enums\AccountsType;
use App\Enums\TransactionsType;
use App\Exceptions\ServiceException;
use App\Models\Account;
use App\Models\AccountGroup;
use App\Models\AccountPivot;
use App\Models\Category;
use App\Models\Transaction;
use Illuminate\Support\Collection;

class AccountService extends BaseService
{
    public function __construct()
    {
        parent::__construct(
            _model: Account::class
        );
    }

    public function store(Collection $data): bool
    {
        $model = $this->getModel()->firstOrCreate([
            'name' => $data->get('name'),
            'type' => $data->get('type')
        ]);
        
        $model->group()->attach($data->get('account_group'), [
            'workspace_id' => session()->get(WorkspaceService::KEY),
            'opening_date' => $data->get('opening_date'),
            'starting_balance' => $data->get('starting_balance'),
            'latest_balance' => $data->get('starting_balance'),
            'currency' => $data->get('currency'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        return !is_null($this->getModel());
    }

    public function update(mixed $model, Collection $data): bool
    {
        $this->verifyModel($model);

        $accountGroup = AccountGroup::currentWorkspace()->select('id', 'name', 'type')->find($data->get('account_group'));

        $this->setModel(
            $this->getModel()->firstOrCreate(
                $data->only('name', 'type')->toArray()
            )
        );
        
        $updatedModel = $this->getModel();

        if($updatedModel->id != $model->id) {
            AccountPivot::where(function($query) use ($accountGroup, $model) {
                $query->where('account_group_id', $accountGroup->id)
                    ->where('account_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'account_id' => $updatedModel->id
            ]);
            
            Transaction::where(function($query) use ($model) {
                $query->where('category_id', $model->id)
                    ->where('workspace_id', session()->get(WorkspaceService::KEY));
            })->update([
                'category_id' => $updatedModel->id
            ]);
        }

        $updatedModel->group()->sync([
            $accountGroup->id => [
                'opening_date' => $data->get('opening_date'),
                'starting_balance' => $data->get('starting_balance'),
                'latest_balance' => $data->get('starting_balance'),
                'currency' => $data->get('currency'),
                'notes' => $data->get('notes'),
            ]
        ]);

        // TODO: Make adjustment on latest_balance

        return true;
    }

    public function destroy(mixed $model): bool
    {
        $this->verifyModel($model);

        $model->group()->detach();
        
        $this->setModel(null);

        // TODO: What happen to transactions

        return true;
    }

    public function updateLatestBalance(mixed $model, float $amount, float $previous_amount = 0): bool
    {
        $this->verifyModel($model);

        if($amount == 0)
            return true;

        $previous_balance = $model->details->latest_balance;

        $is_updated = $model->details->update([
            'latest_balance' => $previous_balance + $amount + $previous_amount
        ]);

        $model->refresh();

        $this->setModel($model);

        return $is_updated;
    }
}