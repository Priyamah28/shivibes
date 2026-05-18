@props(['address' => null, 'showDefault' => true])

<div class="grid gap-4 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Full Name *</label>
        <input type="text" name="full_name" value="{{ old('full_name', $address?->full_name) }}" required class="input-field mt-1">
    </div>
    <div>
        <label class="block text-sm font-medium">Phone *</label>
        <input type="tel" name="phone" value="{{ old('phone', $address?->phone) }}" required class="input-field mt-1" placeholder="10-digit mobile">
    </div>
    <div>
        <label class="block text-sm font-medium">Alternate Phone</label>
        <input type="tel" name="phone_alt" value="{{ old('phone_alt', $address?->phone_alt) }}" class="input-field mt-1">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Address Line 1 *</label>
        <input type="text" name="address_line_1" value="{{ old('address_line_1', $address?->address_line_1) }}" required class="input-field mt-1">
    </div>
    <div class="sm:col-span-2">
        <label class="block text-sm font-medium">Address Line 2</label>
        <input type="text" name="address_line_2" value="{{ old('address_line_2', $address?->address_line_2) }}" class="input-field mt-1">
    </div>
    <div>
        <label class="block text-sm font-medium">Landmark</label>
        <input type="text" name="landmark" value="{{ old('landmark', $address?->landmark) }}" class="input-field mt-1">
    </div>
    <div>
        <label class="block text-sm font-medium">City *</label>
        <input type="text" name="city" value="{{ old('city', $address?->city) }}" required class="input-field mt-1">
    </div>
    <div>
        <label class="block text-sm font-medium">State *</label>
        <select name="state" required class="input-field mt-1">
            <option value="">Select state</option>
            @foreach (config('india.states') as $state)
                <option value="{{ $state }}" @selected(old('state', $address?->state) === $state)>{{ $state }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block text-sm font-medium">Pincode *</label>
        <input type="text" name="pincode" value="{{ old('pincode', $address?->pincode) }}" required pattern="[0-9]{6}" maxlength="6" class="input-field mt-1">
    </div>
    <div>
        <label class="block text-sm font-medium">Country *</label>
        <input type="text" name="country" value="{{ old('country', $address?->country ?? 'India') }}" required class="input-field mt-1">
    </div>
    <div>
        <label class="block text-sm font-medium">Address Type *</label>
        <select name="address_type" required class="input-field mt-1">
            @foreach (\App\Models\CustomerAddress::TYPES as $value => $label)
                <option value="{{ $value }}" @selected(old('address_type', $address?->address_type ?? 'home') === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    @if ($showDefault)
        <div class="sm:col-span-2">
            <label class="flex items-center gap-2 text-sm">
                <input type="hidden" name="is_default" value="0">
                <input type="checkbox" name="is_default" value="1" @checked(old('is_default', $address?->is_default ?? false)) class="rounded border-brand-300 text-brand-700">
                Set as default address
            </label>
        </div>
    @endif
</div>
