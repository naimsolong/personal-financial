<?php

namespace App\Http\Controllers;

use App\Http\Requests\WaitlistFormRequest;
use App\Services\WaitlistService;
use Illuminate\Support\Facades\RateLimiter;

class WaitlistController extends Controller
{
    public function join(WaitlistFormRequest $request)
    {
        $executed = RateLimiter::attempt(
            'join-waitlist:'.$request->email, 5,
            function () use ($request) {
                $waitlistService = app(WaitlistService::class);

                $waitlistService->join($request->email);
            }
        );

        if (! $executed) {
            return response()->json([
                'message' => 'Too many submission sent! Try again later after 10 minutes.',
            ]);
        }

        return response()->json([
            'message' => 'You have been added to the waitlist!',
        ]);
    }
}
