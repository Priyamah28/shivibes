<?php

namespace App\Services;

use App\Models\CustomerAddress;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressService
{
    public function setDefault(User $user, CustomerAddress $address): void
    {
        DB::transaction(function () use ($user, $address) {
            $user->addresses()->whereKeyNot($address->id)->update(['is_default' => false]);
            $address->forceFill(['is_default' => true])->save();
        });
    }

    public function ensureDefaultExists(User $user): void
    {
        if ($user->addresses()->where('is_default', true)->exists()) {
            return;
        }

        $first = $user->addresses()->oldest()->first();
        if ($first) {
            $first->update(['is_default' => true]);
        }
    }
}
