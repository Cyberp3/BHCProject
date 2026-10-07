<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     * Public self-registration is disabled for clinical security.
     */
    public function create(): RedirectResponse
    {
        return redirect()->route('login')
            ->with('error', 'Public self-registration is disabled. Authorized staff accounts are provisioned exclusively by the Health Center Administrator.');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('login')
            ->with('error', 'Public self-registration is disabled. Authorized staff accounts are provisioned exclusively by the Health Center Administrator.');
    }
}
