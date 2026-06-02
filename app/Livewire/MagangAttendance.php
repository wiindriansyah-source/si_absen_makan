<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\MealAttendance;
use App\Models\Visitor;
use Livewire\Attributes\Layout;
use Livewire\Component;

class MagangAttendance extends Component
{
    public $name, $institution, $department_id, $meal_type, $meal_time, $satisfaction, $feedback;
    public $showSuccessModal = false;

    // Otomatis meriset kepuasan & saran jika user mengubah pilihan Meal Type di tengah jalan
    public function updatedMealType($value)
    {
        if ($value === 'Kotakan') {
            $this->satisfaction = null;
            $this->feedback = null;
        }
    }

    // Otomatis meriset saran jika user mengubah kepuasan dari 'Tidak Puas' menjadi 'Puas'
    public function updatedSatisfaction($value)
    {
        if ($value === 'Puas') {
            $this->feedback = null;
        }
    }

    public function save()
    {
        // 1. Array Aturan Utama
        $rules = [
            'name'          => 'required|min:3',
            'institution'   => 'required',
            'department_id' => 'required',
            'meal_type'     => 'required',
            'meal_time'     => 'required',
        ];

        // 2. Kondisional Aturan jika BUKAN Kotakan
        if ($this->meal_type === 'Kantin') {
            $rules['satisfaction'] = 'required';

            // Jika memilih Tidak Puas, Kritik Saran hukumnya Wajib diisi
            if ($this->satisfaction === 'Tidak Puas') {
                $rules['feedback'] = 'required|min:5|max:255';
            } else {
                $rules['feedback'] = 'nullable|max:255';
            }
        }

        // Jalankan Validasi Dinamis
        $this->validate($rules, [
            'required' => 'Kolom ini wajib diisi.',
            'min'      => 'Minimal :min karakter.',
            'max'      => 'Maksimal :max karakter.',
        ]);

        $visitor = Visitor::updateOrCreate(
            ['name' => $this->name, 'institution' => $this->institution],
            ['type' => 'Magang']
        );

        MealAttendance::create([
            'visitor_id'    => $visitor->id,
            'department_id' => $this->department_id,
            'meal_type'     => $this->meal_type,
            'meal_time'     => $this->meal_time,
            // Jika Kotakan, simpan null atau nilai default di database Anda
            'satisfaction'  => $this->meal_type === 'Kotakan' ? 'Puas' : $this->satisfaction,
            'feedback'      => $this->feedback ?? '-',
        ]);

        $this->showSuccessModal = true;

        $this->reset(['name', 'institution', 'department_id', 'meal_type', 'meal_time', 'satisfaction', 'feedback']);
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
        return redirect()->to('/');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.magang-attendance', [
            'departments' => Department::orderBy('name', 'asc')->get()
        ]);
    }
}
