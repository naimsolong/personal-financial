<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Waitlist;
use Inertia\Inertia;

class WaitlistController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Waitlists/Index', [
            'waitlists' => fn () => Waitlist::select('email')->paginate(),
        ]);
    }
}
