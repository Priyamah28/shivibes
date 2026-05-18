@extends('admin.layout')

@section('content')
    @include('admin.partials.page-header', [
        'title' => 'User Accounts',
        'subtitle' => 'Manage storefront customers and admin users. Deactivated users cannot log in.',
        'actionUrl' => route('admin.users.create'),
        'actionLabel' => '+ Add User',
    ])

    <form method="GET" action="{{ route('admin.users.index') }}" class="mb-4 flex flex-wrap items-end gap-3 rounded-2xl border border-brand-100 bg-white p-4 shadow-sm">
        <div class="min-w-[200px] flex-1">
            <label class="block text-xs font-medium text-slate-600">Search</label>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Name or email…" class="input-field mt-1">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600">Role</label>
            <select name="role" class="input-field mt-1">
                <option value="">All roles</option>
                <option value="customer" @selected(request('role') === 'customer')>Customer</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600">Status</label>
            <select name="status" class="input-field mt-1">
                <option value="">All statuses</option>
                <option value="active" @selected(request('status') === 'active')>Active</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Inactive</option>
            </select>
        </div>
        <button type="submit" class="btn-primary">Filter</button>
        @if (request()->hasAny(['q', 'role', 'status']))
            <a href="{{ route('admin.users.index') }}" class="btn-ghost">Clear</a>
        @endif
    </form>

    <div class="overflow-hidden rounded-2xl border border-brand-100 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead class="bg-brand-50 text-xs uppercase text-brand-800">
                <tr>
                    <th class="px-4 py-3">Name</th>
                    <th class="px-4 py-3">Email</th>
                    <th class="px-4 py-3">Role</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Joined</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-brand-50">
                @forelse ($users as $user)
                    <tr class="hover:bg-brand-50/50">
                        <td class="px-4 py-3 font-medium">
                            {{ $user->name }}
                            @if (auth()->id() === $user->id)
                                <span class="ml-1 text-xs text-brand-600">(you)</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-slate-600">{{ $user->email }}</td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $user->isAdmin() ? 'bg-gold-100 text-gold-900' : 'bg-brand-100 text-brand-800' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">
                            <span class="badge {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-slate-100 text-slate-600' }}">
                                {{ $user->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-slate-500">{{ $user->created_at->format('d M Y') }}</td>
                        <td class="px-4 py-3 text-right">
                            <a href="{{ route('admin.users.edit', $user) }}" class="text-brand-700 hover:underline">Edit</a>
                            @if (auth()->id() !== $user->id)
                                <form action="{{ route('admin.users.toggle_active', $user) }}" method="POST" class="mt-1 inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="text-xs {{ $user->is_active ? 'text-rose-600' : 'text-green-700' }} hover:underline">
                                        {{ $user->is_active ? 'Deactivate' : 'Activate' }}
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="px-4 py-12 text-center text-slate-500">No users found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($users->hasPages())
        <div class="mt-4">{{ $users->links() }}</div>
    @endif
@endsection
