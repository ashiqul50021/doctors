<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HealthPackage;
use Illuminate\Http\Request;

class HealthPackageController extends Controller
{
    public function index()
    {
        $packages = HealthPackage::ordered()->get();
        return view('admin.health-packages.index', compact('packages'));
    }

    public function create()
    {
        return view('admin.health-packages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'badge_label' => 'required|string|max:50',
            'icon' => 'required|string|max:100',
            'test_count' => 'required|integer|min:0',
            'features' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_label' => 'required|string|max:50',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $features = array_filter(array_map('trim', explode("\n", $request->features ?? '')));

        HealthPackage::create([
            'title' => $request->title,
            'badge_label' => $request->badge_label,
            'icon' => $request->icon,
            'test_count' => $request->test_count,
            'features' => $features,
            'price' => $request->price,
            'price_label' => $request->price_label,
            'link' => $request->link,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.health-packages.index')->with('success', 'Health Package created successfully.');
    }

    public function edit(HealthPackage $healthPackage)
    {
        return view('admin.health-packages.edit', compact('healthPackage'));
    }

    public function update(Request $request, HealthPackage $healthPackage)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'badge_label' => 'required|string|max:50',
            'icon' => 'required|string|max:100',
            'test_count' => 'required|integer|min:0',
            'features' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'price_label' => 'required|string|max:50',
            'link' => 'nullable|string|max:255',
            'sort_order' => 'nullable|integer',
        ]);

        $features = array_filter(array_map('trim', explode("\n", $request->features ?? '')));

        $healthPackage->update([
            'title' => $request->title,
            'badge_label' => $request->badge_label,
            'icon' => $request->icon,
            'test_count' => $request->test_count,
            'features' => $features,
            'price' => $request->price,
            'price_label' => $request->price_label,
            'link' => $request->link,
            'is_featured' => $request->has('is_featured'),
            'is_active' => $request->has('is_active'),
            'sort_order' => $request->sort_order ?? 0,
        ]);

        return redirect()->route('admin.health-packages.index')->with('success', 'Health Package updated successfully.');
    }

    public function destroy(HealthPackage $healthPackage)
    {
        $healthPackage->delete();
        return redirect()->route('admin.health-packages.index')->with('success', 'Health Package deleted successfully.');
    }
}
