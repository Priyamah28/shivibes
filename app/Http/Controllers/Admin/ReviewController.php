<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function index(Request $request): View
    {
        $status = $request->query('status', 'pending');

        $reviews = Review::query()
            ->with(['product:id,name,slug', 'user:id,name,email'])
            ->when($status === 'pending', fn ($q) => $q->pending())
            ->when($status === 'approved', fn ($q) => $q->approved())
            ->latest()
            ->paginate(20)
            ->withQueryString();

        return view('admin.reviews.index', [
            'reviews' => $reviews,
            'status' => $status,
            'counts' => [
                'pending' => Review::pending()->count(),
                'approved' => Review::approved()->count(),
                'all' => Review::count(),
            ],
        ]);
    }

    public function approve(Review $review): RedirectResponse
    {
        $review->update(['is_approved' => true]);

        return back()->with('success', 'Review approved and is now visible on the product page.');
    }

    public function destroy(Review $review): RedirectResponse
    {
        $review->delete();

        return back()->with('success', 'Review deleted.');
    }
}
