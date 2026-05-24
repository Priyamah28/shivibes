<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductReviewRequest;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\RedirectResponse;

class ProductReviewController extends Controller
{
    public function store(StoreProductReviewRequest $request, string $slug): RedirectResponse
    {
        if (! $request->user()->isCustomer()) {
            return redirect()
                ->route('products.show', $slug)
                ->withFragment('reviews')
                ->with('error', 'Please sign in with a customer account to submit a review.');
        }

        $product = Product::query()
            ->where('slug', $slug)
            ->active()
            ->firstOrFail();

        $exists = Review::query()
            ->where('product_id', $product->id)
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($exists) {
            return redirect()
                ->route('products.show', $slug)
                ->withFragment('reviews')
                ->with('error', 'You have already submitted a review for this product.');
        }

        $validated = $request->validated();

        Review::create([
            'product_id' => $product->id,
            'user_id' => $request->user()->id,
            'reviewer_name' => $request->user()->name,
            'rating' => $validated['rating'],
            'title' => $validated['title'] ?? null,
            'body' => $validated['body'],
            'is_approved' => false,
        ]);

        return redirect()
            ->route('products.show', $slug)
            ->withFragment('reviews')
            ->with('success', 'Thank you! Your review was submitted and will appear after our team approves it.');
    }
}
