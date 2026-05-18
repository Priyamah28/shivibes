<?php

namespace App\Http\Controllers;

use App\Models\CorporateInquiry;
use Illuminate\Http\Request;

class CorporateInquiryController extends Controller
{
    public function create()
    {
        return view('store.corporate');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'gst_number' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'quantity' => 'nullable|integer|min:1',
            'needs_branding' => 'nullable|boolean',
            'callback_requested' => 'nullable|boolean',
            'inquiry_type' => 'nullable|in:corporate,bulk,callback',
            'message' => 'required|string',
        ]);

        $validated['needs_branding'] = $request->boolean('needs_branding');
        $validated['callback_requested'] = $request->boolean('callback_requested');
        $validated['inquiry_type'] = $validated['inquiry_type'] ?? 'corporate';

        CorporateInquiry::create($validated);

        return redirect()->back()->with('success', 'Your inquiry has been submitted successfully. Our corporate team will contact you within 24–48 hours.');
    }
}
