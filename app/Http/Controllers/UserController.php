<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    //
    public function index(): View
    {
        $users = User::orderBy('name')->paginate(15);

        return view('users.index', compact('users'));
    }

    public function create(): View
    {
        $roles = [
            'admin' => 'Administrator',
            'nurse' => 'Barangay Health Nurse',
            'midwife' => 'Barangay Midwife',
            'bns' => 'Barangay Nutrition Scholar (BNS)',
            'bhw' => 'Barangay Health Worker (BHW)',
            'bhv' => 'Barangay Health Volunteer (BHV)',
        ];

        return view('users.create', compact('roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'nurse', 'midwife', 'bns', 'bhw', 'bhv'])],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'contact_number' => $validated['contact_number'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        AuditLog::log(
            'CREATE_USER',
            'Users',
            "Created user account for {$user->name} ({$user->role})",
            $user->id
        );

        return redirect()->route('users.index')
            ->with('success', "User account for {$user->name} created successfully.");
    }

    public function edit(User $user): View
    {
        $roles = [
            'admin' => 'Administrator',
            'nurse' => 'Barangay Health Nurse',
            'midwife' => 'Barangay Midwife',
            'bns' => 'Barangay Nutrition Scholar (BNS)',
            'bhw' => 'Barangay Health Worker (BHW)',
            'bhv' => 'Barangay Health Volunteer (BHV)',
        ];

        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$user->id],
            'password' => ['nullable', 'confirmed', Password::defaults()],
            'role' => ['required', Rule::in(['admin', 'nurse', 'midwife', 'bns', 'bhw', 'bhv'])],
            'contact_number' => ['nullable', 'string', 'max:20'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->role = $validated['role'];
        $user->contact_number = $validated['contact_number'] ?? null;

        // Prevent admin from deactivating their own account
        if ($user->id !== Auth::id()) {
            $user->is_active = $request->boolean('is_active');
        }

        if (! empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        AuditLog::log(
            'UPDATE_USER',
            'Users',
            "Updated user account for {$user->name} ({$user->role})",
            $user->id
        );

        return redirect()->route('users.index')
            ->with('success', "User account for {$user->name} updated successfully.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot delete your own account.']);
        }

        $name = $user->name;
        $id = $user->id;

        $user->delete();

        AuditLog::log(
            'DELETE_USER',
            'Users',
            "Deleted user account for {$name}",
            $id
        );

        return redirect()->route('users.index')
            ->with('success', "User account for {$name} deleted successfully.");
    }
}
