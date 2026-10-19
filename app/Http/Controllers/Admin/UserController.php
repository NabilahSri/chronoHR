<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->query('search');

        $users = User::with('roles')
            ->when($search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('rfid_uid', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(15);

        return view('admin.users.index', compact('users', 'search'));
    }

    public function create(): View
    {
        $roles = Role::all();
        return view('admin.users.create', compact('roles'));
    }

    public function store(UserRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        $validated['password'] = Hash::make($validated['password'] ?? 'password');
        $validated['can_approve_leaves'] = $request->boolean('can_approve_leaves');

        $user = User::create($validated);
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index')->with('success', 'Karyawan berhasil didaftarkan.');
    }

    public function edit(User $user): View
    {
        $roles = Role::all();
        $user->load('roles');
        return view('admin.users.edit', compact('user', 'roles'));
    }

    public function update(UserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();
        $roles = $validated['roles'] ?? [];
        unset($validated['roles']);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $validated['can_approve_leaves'] = $request->boolean('can_approve_leaves');

        $user->update($validated);
        $user->roles()->sync($roles);

        return redirect()->route('admin.users.index')->with('success', 'Data karyawan berhasil diperbarui.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Tidak dapat menghapus akun sendiri.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Karyawan berhasil dihapus.');
    }
}
