@php $user = $user ?? null; @endphp

<div class="max-w-2xl space-y-6 rounded-2xl border border-brand-100 bg-white p-6 shadow-sm">
    <div>
        <label class="block text-sm font-medium">Name *</label>
        <input type="text" name="name" value="{{ old('name', $user?->name) }}" required class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Email *</label>
        <input type="email" name="email" value="{{ old('email', $user?->email) }}" required class="input-field">
    </div>
    <div>
        <label class="block text-sm font-medium">Password {{ $user ? '(leave blank to keep current)' : '*' }}</label>
        <input type="password" name="password" {{ $user ? '' : 'required' }} class="input-field" autocomplete="new-password">
    </div>
    <div>
        <label class="block text-sm font-medium">Confirm password</label>
        <input type="password" name="password_confirmation" {{ $user ? '' : 'required' }} class="input-field" autocomplete="new-password">
    </div>
    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium">Role *</label>
            <select name="role" required class="input-field">
                <option value="customer" @selected(old('role', $user?->role ?? 'customer') === 'customer')>Customer</option>
                <option value="admin" @selected(old('role', $user?->role) === 'admin')>Admin</option>
            </select>
        </div>
        <div class="flex items-end">
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_active" value="0">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $user?->is_active ?? true)) class="rounded border-brand-300 text-brand-700">
                Active account
            </label>
        </div>
    </div>
    <div class="flex gap-3">
        <button type="submit" class="btn-primary">{{ $user ? 'Update User' : 'Create User' }}</button>
        <a href="{{ route('admin.users.index') }}" class="btn-ghost">Cancel</a>
    </div>
</div>
