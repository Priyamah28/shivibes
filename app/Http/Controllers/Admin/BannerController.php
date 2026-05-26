<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\AdminImageUploadService;
use App\Support\AdminImagePresets;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class BannerController extends Controller
{
    public function __construct(
        private readonly AdminImageUploadService $images,
    ) {}

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
        $banner->update($this->validated($request, $banner));

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

    private function validated(Request $request, ?Banner $banner = null): array
    {
        $placement = $request->input('placement', 'home_hero');
        $preset = AdminImagePresets::bannerPresetForPlacement($placement);

        $data = $request->validate(array_merge([
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:500',
            'cta_text' => 'nullable|string|max:100',
            'cta_url' => 'nullable|string|max:500',
            'placement' => 'required|string|in:home_hero,promo_strip',
            'sort_order' => 'nullable|integer|min:0',
            'is_active' => 'nullable|boolean',
        ], $this->images->urlRules(), $this->images->fileRules($preset)));

        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;

        if ($request->hasFile('image_file')) {
            $data['image'] = $this->images->store($request->file('image_file'), $preset);
        }

        if (empty($data['image']) && ! $banner?->image) {
            $this->images->requireImageOrUpload($data, false);
        }

        return $data;
    }
}
