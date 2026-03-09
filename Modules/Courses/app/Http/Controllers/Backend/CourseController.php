<?php

namespace Modules\Courses\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use App\Services\ImageService;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\CourseCategory;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('category', 'instructor')->latest()->get();
        return view('courses::backend.courses.index', compact('courses'));
    }

    public function create()
    {
        $categories = CourseCategory::orderBy('name')->get();
        return view('courses::backend.courses.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_category_id' => 'nullable|exists:course_categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = ImageService::upload($request->file('image'), 'courses');
        }

        Course::create([
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'course_category_id' => $request->course_category_id,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'image' => $imagePath,
            'instructor_id' => auth()->id(),
            'is_active' => true,
            'is_featured' => $request->has('is_featured'),
        ]);

        return redirect()->route('courses.admin.courses.index')->with('success', 'Course created successfully.');
    }

    public function show(Course $course)
    {
        $course->load('lessons');
        return view('courses::backend.courses.show', compact('course'));
    }

    public function edit(Course $course)
    {
        $categories = CourseCategory::orderBy('name')->get();
        return view('courses::backend.courses.edit', compact('course', 'categories'));
    }

    public function update(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'course_category_id' => 'nullable|exists:course_categories,id',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $data = [
            'title' => $request->title,
            'slug' => Str::slug($request->title),
            'course_category_id' => $request->course_category_id,
            'description' => $request->description,
            'price' => $request->price,
            'sale_price' => $request->sale_price,
            'is_active' => $request->has('is_active'),
            'is_featured' => $request->has('is_featured'),
        ];

        if ($request->hasFile('image')) {
            ImageService::delete($course->image);
            $data['image'] = ImageService::upload($request->file('image'), 'courses');
        }

        $course->update($data);

        return redirect()->route('courses.admin.courses.index')->with('success', 'Course updated successfully.');
    }

    public function destroy(Course $course)
    {
        ImageService::delete($course->image);
        $course->delete();

        return redirect()->route('courses.admin.courses.index')->with('success', 'Course deleted successfully.');
    }
}
