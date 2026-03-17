<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::all();
        return view('users.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'nullable|string|max:20',
            'password' => 'required|min:6|confirmed',
            'role'     => 'required|exists:roles,name',
            'status'   => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'phone'    => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'status'   => $data['status'],
        ]);
        $user->assignRole($data['role']);

        ActivityLog::log('create', "Created user: {$user->email}", $user);
        return redirect()->route('users.index')->with('success', 'User created.');
    }

    public function edit(User $user)
    {
        $roles = Role::all();
        return view('users.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'   => 'required|string|max:255',
            'email'  => 'required|email|unique:users,email,'.$user->id,
            'phone'  => 'nullable|string|max:20',
            'role'   => 'required|exists:roles,name',
            'status' => 'required|in:active,inactive',
        ]);

        $user->update(['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'], 'status' => $data['status']]);
        $user->syncRoles([$data['role']]);

        ActivityLog::log('update', "Updated user: {$user->email}", $user);
        return redirect()->route('users.index')->with('success', 'User updated.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);
        ActivityLog::log('update', "Reset password for user: {$user->email}", $user);
        return back()->with('success', 'Password reset successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own account.');
        }
        ActivityLog::log('delete', "Deleted user: {$user->email}", $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'User deleted.');
    }
}
