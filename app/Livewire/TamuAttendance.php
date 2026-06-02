<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\MealAttendance;
use App\Models\Visitor;
use Livewire\Attributes\Layout;
use Livewire\Component;

class TamuAttendance extends Component
{
    public $name, $institution, $department_id, $meal_type, $meal_time, $satisfaction, $feedback;
    public $showSuccessModal = false;
    public $existingVisitors = [];

    public function mount()
    {
        $this->existingVisitors = Visitor::where('type', 'Tamu')
            ->orderBy('name', 'asc')
            ->get();
    }

    // Otomatis meriset kepuasan & saran jika user mengubah pilihan Tipe Makan di tengah jalan
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

    public function selectVisitor($id)
    {
        $v = Visitor::find($id);
        if ($v) {
            $this->name = $v->name;
            $this->institution = $v->institution;
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

        // 2. Kondisional Aturan jika memilih Kantin
        if ($this->meal_type === 'Kantin') {
            $rules['satisfaction'] = 'required';

            // Jika memilih Tidak Puas, Kritik Saran hukumnya Wajib diisi
            if ($this->satisfaction === 'Tidak Puas') {
                $rules['feedback'] = 'required|min:5|max:255';
            } else {
                $rules['feedback'] = 'nullable|max:255';
            }
        }

        // Jalankan Validasi Dinamis dengan Kustom Pesan Error
        $this->validate($rules, [
            'required' => 'Kolom ini wajib diisi.',
            'min'      => 'Minimal :min karakter.',
            'max'      => 'Maksimal :max karakter.',
        ]);

        $visitor = Visitor::updateOrCreate(
            ['name' => $this->name, 'institution' => $this->institution],
            ['type' => 'Tamu']
        );

        MealAttendance::create([
            'visitor_id'    => $visitor->id,
            'department_id' => $this->department_id,
            'meal_type'     => $this->meal_type,
            'meal_time'     => $this->meal_time,
            // Jika Kotakan, otomatis tersimpan 'Puas' di database
            'satisfaction'  => $this->meal_type === 'Kotakan' ? 'Puas' : $this->satisfaction,
            'feedback'      => $this->feedback ?? '-',
        ]);

        $this->showSuccessModal = true;

        // Reset fields
        $this->reset(['name', 'institution', 'department_id', 'meal_type', 'meal_time', 'satisfaction', 'feedback']);
        $this->mount();
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
        return redirect()->to('/');
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.tamu-attendance', [
            'departments' => Department::orderBy('name')->get()
        ]);
    }
}
