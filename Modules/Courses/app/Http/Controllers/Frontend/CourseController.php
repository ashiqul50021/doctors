<?php

namespace Modules\Courses\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Courses\Models\Course;
use Modules\Courses\Models\CourseCategory;
use Modules\Courses\Models\Enrollment;

class CourseController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with('category', 'instructor', 'lessons')->where('is_active', true);

        if ($request->has('category') && $request->category) {
            $query->where('course_category_id', $request->category);
        }

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        $courses = $query->latest()->paginate(12);
        $categories = CourseCategory::orderBy('name')->get();

        return view('courses::frontend.courses.index', compact('courses', 'categories'));
    }

    public function show($id)
    {
        $course = Course::with(['category', 'instructor', 'lessons'])->findOrFail($id);
        $relatedCourses = Course::where('course_category_id', $course->course_category_id)
            ->where('id', '!=', $id)
            ->where('is_active', true)
            ->limit(4)
            ->get();

        return view('courses::frontend.courses.show', compact('course', 'relatedCourses'));
    }

    public function enroll(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        // Check if already enrolled
        $existingEnrollment = Enrollment::where('course_id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($existingEnrollment) {
            return redirect()->back()->with('info', 'You are already enrolled in this course.');
        }

        Enrollment::create([
            'course_id' => $id,
            'user_id' => auth()->id(),
            'status' => 'active',
        ]);

        return redirect()->route('courses.my-courses')->with('success', 'Successfully enrolled!');
    }

    public function myCourses()
    {
        $enrollments = Enrollment::with('course.lessons')
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('courses::frontend.my-courses', compact('enrollments'));
    }

    public function lesson($courseId, $lessonId)
    {
        $course = Course::with('lessons')->findOrFail($courseId);
        $lesson = $course->lessons()->findOrFail($lessonId);

        return view('courses::frontend.lesson', compact('course', 'lesson'));
    }
}
