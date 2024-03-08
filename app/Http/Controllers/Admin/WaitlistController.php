<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use Illuminate\Http\Request;
use Inertia\Inertia;

class WaitlistController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Waitlists/Index', [
            'waitlists' => fn () => Waitlist::select('email')->paginate(),
        ]);
    }

    public function action(Request $request, string $status)
    {
        
    }

    public function bulkAction(Request $request, string $status)
    {

    }

}
