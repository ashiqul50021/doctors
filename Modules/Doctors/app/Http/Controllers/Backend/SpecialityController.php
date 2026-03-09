<?php

namespace Modules\Doctors\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Modules\Doctors\Models\Speciality;

class SpecialityController extends Controller
{
    public function index()
    {
        $specialities = Speciality::latest()->get();
        return view('doctors::backend.specialities.index', compact('specialities'));
    }

    public function create()
    {
        return view('doctors::backend.specialities.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'specialities');
        }

        Speciality::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'image' => $imagePath,
            'is_active' => true,
        ]);

        return redirect()->route('doctors.admin.specialities.index')->with('success', 'Speciality created successfully.');
    }

    public function show(Speciality $speciality)
    {
        return view('doctors::backend.specialities.show', compact('speciality'));
    }

    public function edit(Speciality $speciality)
    {
        return view('doctors::backend.specialities.edit', compact('speciality'));
    }

    public function update(Request $request, Speciality $speciality)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'is_active' => 'boolean',
        ]);

        $data = [
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            ImageService::delete($speciality->image);
            $data['image'] = ImageService::upload($request->file('image'), 'specialities');
        }

        $speciality->update($data);

        return redirect()->route('doctors.admin.specialities.index')->with('success', 'Speciality updated successfully.');
    }

    public function destroy(Speciality $speciality)
    {
        ImageService::delete($speciality->image);
        $speciality->delete();

        return redirect()->route('doctors.admin.specialities.index')->with('success', 'Speciality deleted successfully.');
    }
}
