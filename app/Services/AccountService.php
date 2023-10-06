<?php

namespace App\Services;

use App\Enums\AccountsType;
use App\Exceptions\ServiceException;
use App\Models\Account;
use App\Models\AccountGroup;
use Illuminate\Support\Collection;

class AccountService extends BaseService
{
    public function store(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $accountGroup = AccountGroup::select('id', 'name', 'type')->find($data->get('account_group'));

        $model = $model->firstOrCreate(
            ['name' => $data->get('name')],
            ['type' => $data->get('type')],
        );
        
        $accountGroup->accounts()->attach($model->id, [
            'opening_date' => $data->get('opening_date'),
            'starting_balance' => $data->get('starting_balance'),
            'latest_balance' => $data->get('starting_balance'),
            'currency' => $data->get('currency'),
            'notes' => $data->get('notes'),
        ]);

        $this->setModel($model);

        return !is_null($this->getModel());
    }

    public function update(mixed $model = null, Collection $data): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $accountGroup = AccountGroup::select('id', 'name', 'type')->find($data->get('account_group'));

        $model->update($data->only('name', 'type')->toArray());

        $model->group()->sync([
            $accountGroup->id => [
                'opening_date' => $data->get('opening_date'),
                'starting_balance' => $data->get('starting_balance'),
                'latest_balance' => $data->get('starting_balance'),
                'currency' => $data->get('currency'),
                'notes' => $data->get('notes'),
            ]
        ]);

        $this->setModel($model);

        return !is_null($this->getModel());
    }

    public function destroy(mixed $model = null): bool
    {
        if(is_null($model))
            throw new ServiceException('Model Not Found');

        $model->group()->detach();
        
        $this->setModel(null);

        return is_null($this->getModel());
    }
}