<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Services\ImageService;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $banners = Banner::ordered()->get();
        return view('admin.banners.index', compact('banners'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banners.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'type' => 'required|in:content_image,image_only',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'title' => 'nullable|required_if:type,content_image|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'stats_text' => 'nullable|string|max:100',
            'order' => 'integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');
        $data['image'] = ImageService::upload($request->file('image'), 'banners');

        // For image_only type, use image_link as button_link
        if ($request->type === 'image_only' && $request->image_link) {
            $data['button_link'] = $request->image_link;
        }

        Banner::create($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Banner $banner)
    {
        return view('admin.banners.edit', compact('banner'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'type' => 'required|in:content_image,image_only',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'title' => 'nullable|required_if:type,content_image|string|max:255',
            'subtitle' => 'nullable|string',
            'button_text' => 'nullable|string|max:50',
            'button_link' => 'nullable|string|max:255',
            'stats_text' => 'nullable|string|max:100',
            'order' => 'integer|min:0',
        ]);

        $data = $request->all();
        $data['is_active'] = $request->has('is_active');

        if ($request->hasFile('image')) {
            ImageService::delete($banner->image);
            $data['image'] = ImageService::upload($request->file('image'), 'banners');
        }

        // For image_only type, use image_link as button_link
        if ($request->type === 'image_only' && $request->image_link) {
            $data['button_link'] = $request->image_link;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index')->with('success', 'Banner updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Banner $banner)
    {
        ImageService::delete($banner->image);
        $banner->delete();

        return redirect()->route('admin.banners.index')->with('success', 'Banner deleted successfully.');
    }
}
