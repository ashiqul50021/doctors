<?php

use Illuminate\Support\Facades\Route;
use Modules\Courses\Http\Controllers\Frontend\CourseController;
use Modules\Courses\Http\Controllers\Backend\CourseController as AdminCourseController;

/*
|--------------------------------------------------------------------------
| Courses Frontend Routes
|--------------------------------------------------------------------------
*/

Route::prefix('courses')->name('courses.')->group(function () {
    Route::get('/', [CourseController::class, 'index'])->name('index');
    Route::get('/{id}', [CourseController::class, 'show'])->name('show');
    Route::post('/{id}/enroll', [CourseController::class, 'enroll'])->name('enroll')->middleware('auth');
    Route::get('/{courseId}/lesson/{lessonId}', [CourseController::class, 'lesson'])->name('lesson');
});

Route::get('/my-courses', [CourseController::class, 'myCourses'])->name('courses.my-courses')->middleware('auth');

/*
|--------------------------------------------------------------------------
| Courses Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])->prefix('admin')->name('courses.admin.')->group(function () {
    Route::resource('courses', AdminCourseController::class);
});
