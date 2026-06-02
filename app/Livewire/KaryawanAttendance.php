<?php

namespace App\Livewire;

use App\Models\Department;
use App\Models\Employee;
use App\Models\MealAttendance;
use Livewire\Attributes\Layout;
use Livewire\Component;

class KaryawanAttendance extends Component
{
    // Form States
    public $department_id;
    public $employee_id;
    public $meal_type;
    public $meal_time;
    public $satisfaction;
    public $feedback;

    public $showSuccessModal = false;
    // Data Collections
    public $employees = [];

    public function updatedDepartmentId($value)
    {
        // Update daftar karyawan saat departemen dipilih
        $this->employees = Employee::where('department_id', $value)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        $this->employee_id = null; // Reset pilihan nama
    }

    // Mengosongkan kepuasan & kritik jika user beralih ke Kotakan
    public function updatedMealType($value)
    {
        if ($value === 'Kotakan') {
            $this->satisfaction = null;
            $this->feedback = null;
        }
    }

    // Mengosongkan kritik jika user beralih memilih Puas
    public function updatedSatisfaction($value)
    {
        if ($value === 'Puas') {
            $this->feedback = null;
        }
    }

    public function save()
    {
        // 1. Aturan Validasi Utama
        $rules = [
            'employee_id'   => 'required',
            'department_id' => 'required',
            'meal_type'     => 'required',
            'meal_time'     => 'required',
        ];

        // 2. Aturan Validasi Kondisional (Logika tipe makan)
        if ($this->meal_type === 'Kantin') {
            $rules['satisfaction'] = 'required';

            if ($this->satisfaction === 'Tidak Puas') {
                $rules['feedback'] = 'required|min:5|max:255';
            } else {
                $rules['feedback'] = 'nullable|max:255';
            }
        }

        // Jalankan validasi dengan custom message bahasa Indonesia
        $this->validate($rules, [
            'required' => 'Kolom ini wajib diisi.',
            'min'      => 'Minimal wajib diisi :min karakter.',
            'max'      => 'Maksimal diisi :max karakter.',
        ]);

        MealAttendance::create([
            'employee_id'   => $this->employee_id,
            'department_id' => $this->department_id,
            'meal_type'     => $this->meal_type,
            'meal_time'     => $this->meal_time,
            // Jika memilih Kotakan, otomatis nilai kepuasan diset 'Puas' di database
            'satisfaction'  => $this->meal_type === 'Kotakan' ? 'Puas' : $this->satisfaction,
            'feedback'      => $this->feedback ?? '-',
        ]);

        $this->showSuccessModal = true;

        // Reset form agar bersih kembali
        $this->reset(['employee_id', 'department_id', 'meal_type', 'meal_time', 'satisfaction', 'feedback', 'employees']);
    }

    public function closeModal()
    {
        $this->showSuccessModal = false;
        return redirect()->to('/'); // Balik ke halaman awal setelah tutup modal
    }

    #[Layout('components.layouts.app')]
    public function render()
    {
        return view('livewire.karyawan-attendance', [
            'departments' => Department::orderBy('name')->get()
        ]);
    }
}
