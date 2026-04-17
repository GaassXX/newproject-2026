<?php

namespace App\Http\Controllers;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of users.
     */
    public function index()
    {
        $users = User::with('roles')->paginate(15);
        $roles = Role::all();

        return view('admin.users.index', compact('users', 'roles'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles()->pluck('id')->toArray();

        return view('admin.users.edit', compact('user', 'roles', 'userRoles'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'array|nullable'
        ]);

        $user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Sync roles: convert role IDs to role names/objects
        if ($request->has('roles') && is_array($request->input('roles'))) {
            $roleIds = array_filter($request->input('roles'));
            if (!empty($roleIds)) {
                // Get Role objects by ID and sync
                $roles = Role::whereIn('id', $roleIds)->get();
                $user->syncRoles($roles);
            } else {
                // Clear all roles if empty
                $user->syncRoles([]);
            }
        } else {
            // Clear all roles if not provided
            $user->syncRoles([]);
        }

        return redirect()->route('users.index')
            ->with('success', 'User updated successfully.');
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id'
        ]);

        $role = Role::find($validated['role_id']);

        if ($user->hasRole($role)) {
            $user->removeRole($role);
            $message = "Role '{$role->name}' removed from user.";
        } else {
            $user->assignRole($role);
            $message = "Role '{$role->name}' assigned to user.";
        }

        return back()->with('success', $message);
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Cannot delete your own account.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'User deleted successfully.');
    }
}
