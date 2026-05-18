<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use App\Http\Requests\CustomerAddressRequest;
use App\Models\CustomerAddress;
use App\Services\AddressService;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AddressController extends Controller
{
    public function __construct(
        private readonly AddressService $addressService,
        private readonly CheckoutService $checkoutService,
    ) {}

    public function index(Request $request): View
    {
        $this->authorize('viewAny', CustomerAddress::class);

        return view('account.addresses.index', [
            'addresses' => $request->user()->addresses()->orderByDesc('is_default')->orderBy('full_name')->get(),
        ]);
    }

    public function create(): View
    {
        $this->authorize('create', CustomerAddress::class);

        return view('account.addresses.create');
    }

    public function store(CustomerAddressRequest $request): RedirectResponse
    {
        $this->authorize('create', CustomerAddress::class);

        $isDefault = filter_var($request->input('is_default', false), FILTER_VALIDATE_BOOL);

        $attributes = $this->checkoutService->addressAttributes(array_merge(
            $request->validated(),
            ['set_as_default' => false]
        ));

        $address = $request->user()->addresses()->create($attributes);

        if ($isDefault || $request->user()->addresses()->count() === 1) {
            $this->addressService->setDefault($request->user(), $address);
        }

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Address saved successfully.');
    }

    public function edit(CustomerAddress $address): View
    {
        $this->authorize('update', $address);

        return view('account.addresses.edit', compact('address'));
    }

    public function update(CustomerAddressRequest $request, CustomerAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);

        $isDefault = $request->has('is_default') && $request->input('is_default') !== '0' && $request->input('is_default') !== 0;

        $address->update($this->checkoutService->addressAttributes(array_merge(
            $request->validated(),
            ['set_as_default' => $isDefault]
        )));

        if ($isDefault) {
            $this->addressService->setDefault($request->user(), $address);
        }

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Address updated successfully.');
    }

    public function destroy(CustomerAddress $address): RedirectResponse
    {
        $this->authorize('delete', $address);

        $user = $address->user;
        $wasDefault = $address->is_default;
        $address->delete();

        if ($wasDefault) {
            $this->addressService->ensureDefaultExists($user);
        }

        return redirect()
            ->route('account.addresses.index')
            ->with('success', 'Address removed.');
    }

    public function makeDefault(CustomerAddress $address): RedirectResponse
    {
        $this->authorize('update', $address);

        $this->addressService->setDefault($address->user, $address);

        return back()->with('success', 'Default address updated.');
    }
}
