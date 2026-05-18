<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class ProductController extends Controller
{
    private const TYPES = [
        'personal' => 'Personal Gifting',
        'corporate' => 'Corporate Gifting',
        'festival' => 'Festival Collections',
        'combo' => 'Combo Packs',
    ];

    public function index(Request $request): View
    {
        $query = Product::with('categories')->latest();

        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }

        if ($request->filled('category')) {
            $query->whereHas('categories', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('q')) {
            $term = $request->q;
            $query->where(fn ($q) => $q->where('name', 'like', "%{$term}%")->orWhere('sku', 'like', "%{$term}%"));
        }

        return view('admin.products.index', [
            'products' => $query->paginate(15)->withQueryString(),
            'categories' => Category::orderBy('sort_order')->get(),
            'types' => self::TYPES,
            'filters' => $request->only(['type', 'category', 'q']),
        ]);
    }

    public function create(): View
    {
        return view('admin.products.create', $this->formData());
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        $product = Product::create($data);
        $product->categories()->sync($categoryIds);

        return redirect()->route('admin.products.index')->with('success', 'Product created successfully.');
    }

    public function edit(Product $product): View
    {
        $product->load('categories');

        return view('admin.products.edit', array_merge($this->formData(), [
            'product' => $product,
            'selectedCategories' => $product->categories->pluck('id')->all(),
        ]));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $data = $this->validated($request, $product);
        $categoryIds = $data['category_ids'] ?? [];
        unset($data['category_ids']);

        $product->update($data);
        $product->categories()->sync($categoryIds);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->orderItems()->exists()) {
            return back()->with('error', 'Cannot delete a product that has orders. Deactivate it instead.');
        }

        $product->categories()->detach();
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Product deleted.');
    }

    private function formData(): array
    {
        return [
            'categories' => Category::orderBy('sort_order')->get(),
            'types' => self::TYPES,
        ];
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:products,slug,' . ($product?->id ?? 'NULL'),
            'sku' => 'nullable|string|max:100|unique:products,sku,' . ($product?->id ?? 'NULL'),
            'description' => 'required|string',
            'ingredients' => 'nullable|string',
            'usage_instructions' => 'nullable|string',
            'benefits' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'compare_at_price' => 'nullable|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:4096',
            'product_type' => 'required|in:personal,corporate,festival,combo',
            'moq' => 'nullable|integer|min:1',
            'tags' => 'nullable|string|max:500',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'category_ids' => 'nullable|array',
            'category_ids.*' => 'exists:categories,id',
            'is_active' => 'nullable|boolean',
            'is_featured' => 'nullable|boolean',
            'is_bestseller' => 'nullable|boolean',
            'is_trending' => 'nullable|boolean',
        ]);

        $validated['slug'] = Str::slug($validated['slug'] ?: $validated['name']);
        $validated['sku'] = $validated['sku'] ?: strtoupper(Str::slug($validated['slug'], '_'));
        $validated['is_active'] = $request->boolean('is_active');
        $validated['is_featured'] = $request->boolean('is_featured');
        $validated['is_bestseller'] = $request->boolean('is_bestseller');
        $validated['is_trending'] = $request->boolean('is_trending');
        $validated['moq'] = $validated['moq'] ?? 1;

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $filename = time() . '_' . $image->getClientOriginalName();
            
            $destination = base_path('public_html/storage/products');
            if (!file_exists($destination)){ mkdir($destination, 0775, true);}
            $image->move($destination,$filename);
            $validated['image'] = '/storage/products/' . $filename;

            //$validated['image'] = '/storage/' . $request->file('image_file')->store('products', 'public');
        }

        

        $validated['tags'] = collect(explode(',', $validated['tags'] ?? ''))
            ->map(fn ($t) => trim($t))
            ->filter()
            ->values()
            ->all();

        return $validated;
    }
}
