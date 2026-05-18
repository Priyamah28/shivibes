<?php

namespace App\Http\Requests;

use App\Models\CustomerAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerAddressRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:120'],
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{10,20}$/'],
            'phone_alt' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{10,20}$/'],
            'address_line_1' => ['required', 'string', 'max:255'],
            'address_line_2' => ['nullable', 'string', 'max:255'],
            'landmark' => ['nullable', 'string', 'max:120'],
            'city' => ['required', 'string', 'max:100'],
            'state' => ['required', 'string', 'max:100'],
            'country' => ['required', 'string', 'max:100'],
            'pincode' => ['required', 'string', 'max:10', 'regex:/^[0-9]{6}$/'],
            'address_type' => ['required', Rule::in(array_keys(CustomerAddress::TYPES))],
            'is_default' => ['nullable', 'boolean'],
        ];
    }
}
