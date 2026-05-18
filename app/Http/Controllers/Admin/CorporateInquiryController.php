<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CorporateInquiry;
use Illuminate\Http\Request;

class CorporateInquiryController extends Controller
{
    public function index()
    {
        $inquiries = CorporateInquiry::latest()->paginate(20);
        return view('admin.inquiries.index', compact('inquiries'));
    }

    public function updateStatus(Request $request, CorporateInquiry $inquiry)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,in_progress,resolved',
        ]);

        $inquiry->update($validated);

        return redirect()->back()->with('success', 'Inquiry status updated successfully.');
    }
}
