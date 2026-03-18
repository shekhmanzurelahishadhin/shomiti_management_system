<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Member;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['roles','member'])->latest()->paginate(20);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        $roles   = Role::all();
        $members = Member::where('status','active')
                         ->whereNotIn('id', User::whereNotNull('member_id')->pluck('member_id'))
                         ->orderBy('name')->get();
        return view('users.create', compact('roles','members'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users',
            'phone'     => 'nullable|string|max:20',
            'password'  => 'required|min:6|confirmed',
            'role'      => 'required|exists:roles,name',
            'status'    => 'required|in:active,inactive',
            'member_id' => 'nullable|exists:members,id',
        ]);

        $user = User::create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'] ?? null,
            'password'  => Hash::make($data['password']),
            'status'    => $data['status'],
            'member_id' => $data['member_id'] ?? null,
        ]);
        $user->assignRole($data['role']);

        ActivityLog::log('create', "Created user: {$user->email}", $user);
        return redirect()->route('users.index')->with('success', 'ব্যবহারকারী তৈরি হয়েছে।');
    }

    public function edit(User $user)
    {
        $roles   = Role::all();
        $members = Member::where('status','active')
                         ->whereNotIn('id', User::whereNotNull('member_id')
                             ->where('id','!=',$user->id)
                             ->pluck('member_id'))
                         ->orderBy('name')->get();
        return view('users.edit', compact('user','roles','members'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:255',
            'email'     => 'required|email|unique:users,email,'.$user->id,
            'phone'     => 'nullable|string|max:20',
            'role'      => 'required|exists:roles,name',
            'status'    => 'required|in:active,inactive',
            'member_id' => 'nullable|exists:members,id',
        ]);

        $user->update([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'phone'     => $data['phone'],
            'status'    => $data['status'],
            'member_id' => $data['member_id'] ?? null,
        ]);
        $user->syncRoles([$data['role']]);

        ActivityLog::log('update', "Updated user: {$user->email}", $user);
        return redirect()->route('users.index')->with('success', 'ব্যবহারকারী আপডেট হয়েছে।');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate(['password' => 'required|min:6|confirmed']);
        $user->update(['password' => Hash::make($request->password)]);
        ActivityLog::log('update', "Reset password for user: {$user->email}", $user);
        return back()->with('success', 'পাসওয়ার্ড রিসেট সম্পন্ন।');
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'নিজের অ্যাকাউন্ট মুছে ফেলা যাবে না।');
        }
        ActivityLog::log('delete', "Deleted user: {$user->email}", $user);
        $user->delete();
        return redirect()->route('users.index')->with('success', 'ব্যবহারকারী মুছে ফেলা হয়েছে।');
    }
}
