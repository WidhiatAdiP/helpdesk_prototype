<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index()
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        return Inertia::render('Users/Index', [
            'users' => User::oldest()->get(),
        ]);
    }

    public function create()
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        return Inertia::render('Users/Create');
    }

    public function store(Request $request)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        $validated = $request->validate([
            'name' => ['required', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'role' => ['required', 'in:admin,agent,user'],
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        return Inertia::render('Users/Edit', [
            'user' => $user,
        ]);
    }

    public function update(Request $request, User $user)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        $validated = $request->validate([
            'name' => ['required'],
            'email' => ['required', 'email', 'unique:users,email,' . $user->id],
            'role' => ['required', 'in:admin,agent,user'],
        ]);

        // Cegah admin menurunkan role akun miliknya sendiri,
        // supaya tidak terkunci dari akses admin.
        if (
            $user->id === auth()->id()
            && $user->role === 'admin'
            && $validated['role'] !== 'admin'
        ) {
            return back()->with(
                'error',
                'You cannot change your own role away from Admin.'
            );
        }

        $user->update($validated);

        return back()->with('success', 'User updated successfully.');
    }

    public function editPassword(User $user)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        return Inertia::render('Users/Password', [
            'user' => $user,
        ]);
    }

    public function updatePassword(Request $request, User $user)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        $validated = $request->validate([
            'password' => ['required', 'confirmed', 'min:6'],
        ]);

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()
            ->route('users.index')
            ->with('success', 'Password updated successfully.');
    }

    public function destroy(User $user)
    {
        abort_unless(
            auth()->user()->isAdmin(),
            403
        );

        if ($user->id === auth()->id()) {
            return back()->with(
                'error',
                'You cannot delete your own account.'
            );
        }

        $user->delete();

        return redirect()
            ->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}