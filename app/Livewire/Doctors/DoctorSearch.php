<?php

namespace App\Livewire\Doctors;

use Livewire\Component;
use Livewire\WithPagination;
use Modules\Doctors\Entities\Doctor;

class DoctorSearch extends Component
{
    use WithPagination;

    public $search = '';
    public $speciality_id = '';
    public $district_id = '';
    public $gender = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'speciality_id' => ['except' => ''],
        'gender' => ['except' => ''],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Doctor::query();

        if (!empty($this->search)) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('designation', 'like', '%' . $this->search . '%');
        }

        if ($this->speciality_id !== '') {
            $query->where('speciality_id', $this->speciality_id);
        }

        if ($this->gender !== '') {
            $query->where('gender', $this->gender);
        }

        $doctors = $query->paginate(10);

        return view('components.doctors.doctor-search', [
            'doctors' => $doctors
        ]);
    }
}
