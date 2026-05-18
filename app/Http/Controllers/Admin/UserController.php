<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(Request $request): View
    {
        $query = User::query()->orderByDesc('created_at');

        if ($search = $request->string('q')->trim()->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->string('role')->toString()) {
            if (in_array($role, [User::ROLE_ADMIN, User::ROLE_CUSTOMER], true)) {
                $query->where('role', $role);
            }
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->string('status')->toString() === 'active');
        }

        return view('admin.users.index', [
            'users' => $query->paginate(20)->withQueryString(),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.create');
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $user = User::create([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
            'password' => Hash::make($request->string('password')->toString()),
        ]);

        $user->forceFill([
            'role' => $request->string('role')->toString(),
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    public function edit(User $user): View
    {
        return view('admin.users.edit', compact('user'));
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $role = $request->string('role')->toString();
        $isActive = $request->boolean('is_active');

        if ($error = $this->guardAgainstLockout($user, $role, $isActive)) {
            return back()->withInput()->with('error', $error);
        }

        $user->fill([
            'name' => $request->string('name')->toString(),
            'email' => $request->string('email')->toString(),
        ]);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->string('password')->toString());
        }

        $user->forceFill([
            'role' => $role,
            'is_active' => $isActive,
        ])->save();

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    public function toggleActive(User $user): RedirectResponse
    {
        $newStatus = ! $user->is_active;

        if ($error = $this->guardAgainstLockout($user, $user->role, $newStatus)) {
            return back()->with('error', $error);
        }

        $user->forceFill(['is_active' => $newStatus])->save();

        $label = $newStatus ? 'activated' : 'deactivated';

        return back()->with('success', "User {$label} successfully.");
    }

    private function guardAgainstLockout(User $user, string $role, bool $isActive): ?string
    {
        $actor = auth()->user();

        if ($actor && $actor->id === $user->id) {
            if (! $isActive) {
                return 'You cannot deactivate your own account.';
            }

            if ($role !== User::ROLE_ADMIN) {
                return 'You cannot remove your own admin access.';
            }
        }

        if ($this->wouldRemoveLastActiveAdmin($user, $role, $isActive)) {
            return 'At least one active admin account is required.';
        }

        return null;
    }

    private function wouldRemoveLastActiveAdmin(User $user, string $role, bool $isActive): bool
    {
        if (! $user->isAdmin() || ! $user->is_active) {
            return false;
        }

        $willRemainAdmin = $isActive && $role === User::ROLE_ADMIN;

        if ($willRemainAdmin) {
            return false;
        }

        return User::activeAdmins()->whereKeyNot($user->id)->doesntExist();
    }
}
