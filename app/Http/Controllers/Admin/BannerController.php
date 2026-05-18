<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function index(): View
    {
        return view('admin.banners.index', [
            'banners' => Banner::orderBy('sort_order')->get(),
        ]);
    }

    public function create(): View
    {
        return view('admin.banners.create', ['placements' => $this->placements()]);
    }

    public function store(Request $request): RedirectResponse
    {
        Banner::create($this->validated($request));

        return redirect()->route('admin.banners.index')->with('success', 'Banner created.');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', [
            'banner' => $banner,
            'placements' => $this->placements(),
        ]);
    }

    public function update(Request $request, Banner $banner): RedirectResponse
    {
        $banner->update($this->validated($request));

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated.');
    }

    public function destroy(Banner $banner): RedirectResponse
    {
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted.');
    }

    private function placements(): array
    {
        return [
            'home_hero' => 'Homepage Hero Carousel',
            'promo_strip' => 'Promo Strip',
        ];
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'image' => 'nullable|string|max:500',
            'image_file' => 'nullable|image|max:5120',
            'placement' => 'required|string|max:50',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image_file')) {
            $image = $request->file('image_file');
            $filename = time() . '_' . $image->getClientOriginalName();
            $destination = base_path('public_html/storage/banners');      
            if (!file_exists($destination)) {
                mkdir($destination, 0775, true);
            }
         $image->move($destination, $filename);
            $data['image'] = '/storage/banners/' . $filename;
        }

        if (empty($data['image'])) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'image' => 'Provide an image URL or upload a file.',
            ]);
        }

        return $data;
    }
}
