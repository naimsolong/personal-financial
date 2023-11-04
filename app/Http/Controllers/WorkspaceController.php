<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class WorkspaceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $workspaces = Workspace::currentUser()->select('id', 'name', 'slug')->orderBy('name')->get();

        return Inertia::render('Dashboard/Workspaces/Index', [
            'workspaces' => $workspaces->toArray(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('Dashboard/Workspaces/Form', [
            'data' => [
                'id' => '',
                'name' => ''
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(WorkspaceFormRequest $request)
    {
        $workspaceService = app(WorkspaceService::class);
        
        $workspaceService->store(collect($request->only('name')));

        $workspaceService->change($workspaceService->getModel()->id);

        return Redirect::route('workspaces.index');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Workspace $workspace)
    {
        return Inertia::render('Dashboard/Workspaces/Form', [
            'edit_mode' => true,
            'data' => $workspace->only('id','name'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(WorkspaceFormRequest $request, Workspace $workspace)
    {
        app(WorkspaceService::class)->update($workspace, collect($request->only('name')));

        return Redirect::route('workspaces.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Workspace $workspace)
    {
        $workspaceService = app(WorkspaceService::class)->setModel($workspace);
        
        $errors = [];

        if($workspaceService->haveCategoryGroup())
            $errors[] = 'Categories';

        if($workspaceService->haveAccountGroup())
            $errors[] = 'Accounts';

        if($workspaceService->haveTransaction())
            $errors[] = 'Transactions';

        if(count($errors) > 0) {
            if(count($errors) > 1) {
                $last = array_pop($errors);

                $populate = implode(', ', $errors);

                $populate .= ' and ' . $last;
            } else {
                $populate = $errors[0];
            }

            throw ValidationException::withMessages(['custom' => 'Unable to delete workspace because it has ' . $populate . ' in it.']);
        }

        $workspaceService->destroy($workspace);

        $workspaceService->initiate();

        return Redirect::route('workspaces.index');
    }

    /**
     * Change the specified resource inside the session.
     */
    public function change(Request $request)
    {
        Validator::make($request->all(), [
            'workspace_id' => 'required',
        ])->validate();

        app(WorkspaceService::class)->change($request->workspace_id);

        return Redirect::back();
    }
}
