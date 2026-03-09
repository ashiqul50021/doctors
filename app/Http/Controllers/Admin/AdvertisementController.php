<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Advertisement;
use App\Models\Speciality;
use Illuminate\Http\Request;
use App\Services\ImageService;

class AdvertisementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $advertisements = Advertisement::with('speciality')->latest()->get();
        return view('admin.advertisements.index', compact('advertisements'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $specialities = Speciality::where('is_active', true)->get();
        return view('admin.advertisements.create', compact('specialities'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'link' => 'nullable|url',
            'speciality_id' => 'nullable|exists:specialities,id',
            'priority' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'advertisements');
        }

        Advertisement::create([
            'title' => $request->title,
            'image' => $imagePath,
            'link' => $request->link,
            'speciality_id' => $request->speciality_id,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Advertisement $advertisement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Advertisement $advertisement)
    {
        $specialities = Speciality::where('is_active', true)->get();
        return view('admin.advertisements.edit', compact('advertisement', 'specialities'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Advertisement $advertisement)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:20480',
            'link' => 'nullable|url',
            'speciality_id' => 'nullable|exists:specialities,id',
            'priority' => 'required|integer|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'title' => $request->title,
            'link' => $request->link,
            'speciality_id' => $request->speciality_id,
            'priority' => $request->priority,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active'),
        ];

        if ($request->hasFile('image')) {
            if ($advertisement->image) {
                ImageService::delete($advertisement->image);
            }
            $data['image'] = ImageService::upload($request->file('image'), 'advertisements');
        }

        $advertisement->update($data);

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Advertisement $advertisement)
    {
        if ($advertisement->image) {
            ImageService::delete($advertisement->image);
        }
        $advertisement->delete();

        return redirect()->route('admin.advertisements.index')->with('success', 'Advertisement deleted successfully.');
    }
}
