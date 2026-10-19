<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveBanner;
use Illuminate\Http\Request;

class LiveBannerController extends Controller
{
    public function index()
    {
        $banners = LiveBanner::latest()->paginate(15);

        return view('backend.live_banners.index', compact('banners'));
    }

    public function create()
    {
        return view('backend.live_banners.form', ['banner' => new LiveBanner()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateBanner($request);
        $validated['is_active'] = $request->boolean('is_active');
        $validated['image'] = $this->storeImage($request);

        LiveBanner::create($validated);

        return redirect()->route('admin.banners.index')->with('success', _lang('Banner saved successfully'));
    }

    public function edit(LiveBanner $banner)
    {
        return view('backend.live_banners.form', compact('banner'));
    }

    public function update(Request $request, LiveBanner $banner)
    {
        $validated = $this->validateBanner($request);
        $validated['is_active'] = $request->boolean('is_active');
        $oldImage = $banner->image;
        $newImage = $this->storeImage($request);
        if ($newImage) {
            $validated['image'] = $newImage;
        }

        $banner->update($validated);

        if ($newImage && $oldImage) {
            $oldImagePath = public_path('uploads/live-banners/' . basename($oldImage));
            if (is_file($oldImagePath)) {
                @unlink($oldImagePath);
            }
        }

        return redirect()->route('admin.banners.index')->with('success', _lang('Banner updated successfully'));
    }

    public function destroy(LiveBanner $banner)
    {
        $banner->delete();

        if ($banner->image) {
            $imagePath = public_path('uploads/live-banners/' . basename($banner->image));
            if (is_file($imagePath)) {
                @unlink($imagePath);
            }
        }

        return redirect()->route('admin.banners.index')->with('success', _lang('Banner deleted successfully'));
    }

    private function validateBanner(Request $request): array
    {
        return $request->validate([
            'title' => 'required|string|max:150',
            'subtitle' => 'nullable|string|max:200',
            'description' => 'nullable|string|max:2000',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
            'button_text' => 'nullable|string|max:50',
            'button_url' => 'nullable|url|max:2048',
        ]);
    }

    private function storeImage(Request $request): ?string
    {
        if (!$request->hasFile('image')) {
            return null;
        }

        $directory = public_path('uploads/live-banners');
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $file = $request->file('image');
        $filename = uniqid('banner_', true) . '.' . $file->getClientOriginalExtension();
        $file->move($directory, $filename);

        return $filename;
    }
}
