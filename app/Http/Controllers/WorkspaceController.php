<?php

namespace App\Http\Controllers;

use App\Http\Requests\WorkspaceFormRequest;
use App\Models\Workspace;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
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
        app(WorkspaceService::class)->store(Workspace::query(), collect($request->only('name')));

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
        app(WorkspaceService::class)->destroy($workspace);

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
