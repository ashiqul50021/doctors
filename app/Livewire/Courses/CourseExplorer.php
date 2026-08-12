<?php

namespace App\Livewire\Courses;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Courses\Entities\Course;

class CourseExplorer extends Component
{
    use WithPagination;

    public $search = '';
    public $category_id = '';
    public $level = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Course::query();

        if (!empty($this->search)) {
            $query->where('title', 'like', '%' . $this->search . '%');
        }

        $courses = $query->paginate(9);

        return view('components.courses.course-explorer', [
            'courses' => $courses
        ]);
    }
}
