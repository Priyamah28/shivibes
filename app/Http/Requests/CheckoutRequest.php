<?php

namespace App\Http\Requests;

use App\Models\CustomerAddress;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CheckoutRequest extends FormRequest
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
        $rules = [
            'delivery_type' => ['required', 'in:normal,express'],
            'notes' => ['nullable', 'string', 'max:500'],
            'customer_address_id' => [
                'nullable',
                'integer',
                Rule::exists('customer_addresses', 'id')->where('user_id', $this->user()->id),
                Rule::requiredIf(fn () => ! $this->filled('full_name')),
            ],
        ];

        if (! $this->filled('customer_address_id')) {
            $rules = array_merge($rules, [
                'full_name' => ['required', 'string', 'max:120'],
                'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\-\s]{10,20}$/'],
                'phone_alt' => ['nullable', 'string', 'max:20'],
                'address_line_1' => ['required', 'string', 'max:255'],
                'address_line_2' => ['nullable', 'string', 'max:255'],
                'landmark' => ['nullable', 'string', 'max:120'],
                'city' => ['required', 'string', 'max:100'],
                'state' => ['required', 'string', 'max:100'],
                'country' => ['nullable', 'string', 'max:100'],
                'pincode' => ['required', 'string', 'max:10', 'regex:/^[0-9]{6}$/'],
                'address_type' => ['required', Rule::in(array_keys(CustomerAddress::TYPES))],
                'save_address' => ['nullable', 'boolean'],
                'set_as_default' => ['nullable', 'boolean'],
            ]);
        }

        return $rules;
    }
}
