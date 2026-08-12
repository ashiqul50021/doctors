<?php

namespace App\Livewire\Courses;

use Livewire\Component;

class LessonPlayer extends Component
{
    public $course_id;
    public $active_lesson_index = 0;
    public $lessons = [
        ['id' => 1, 'title' => '1. Introduction to Medical Terminology', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'duration' => '12 mins'],
        ['id' => 2, 'title' => '2. Clinical Examination Fundamentals', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'duration' => '18 mins'],
        ['id' => 3, 'title' => '3. Pharmacology Basics & Dosage', 'video_url' => 'https://www.youtube.com/embed/dQw4w9WgXcQ', 'duration' => '25 mins'],
    ];

    public function selectLesson($index)
    {
        $this->active_lesson_index = $index;
    }

    public function render()
    {
        return view('components.courses.lesson-player');
    }
}
