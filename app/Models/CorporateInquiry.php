<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CorporateInquiry extends Model
{
    protected $fillable = [
        'name',
        'company_name',
        'gst_number',
        'email',
        'phone',
        'quantity',
        'needs_branding',
        'callback_requested',
        'inquiry_type',
        'message',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'needs_branding' => 'boolean',
            'callback_requested' => 'boolean',
        ];
    }
}
