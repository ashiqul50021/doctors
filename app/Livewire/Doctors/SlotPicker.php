<?php

namespace App\Livewire\Doctors;

use Livewire\Component;

class SlotPicker extends Component
{
    public $doctor_id;
    public $selected_date;
    public $selected_slot = null;
    public $slots = [
        '10:00 AM', '10:30 AM', '11:00 AM', 
        '04:00 PM', '04:30 PM', '05:00 PM', '05:30 PM'
    ];

    public function mount($doctorId = null)
    {
        $this->doctor_id = $doctorId;
        $this->selected_date = date('Y-m-d');
    }

    public function selectSlot($slot)
    {
        $this->selected_slot = $slot;
    }

    public function confirmBooking()
    {
        if (!$this->selected_slot) {
            session()->flash('error', 'Please select a time slot.');
            return;
        }

        session()->flash('message', "Appointment booked successfully for {$this->selected_date} at {$this->selected_slot}.");
    }

    public function render()
    {
        return view('components.doctors.slot-picker');
    }
}
