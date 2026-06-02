<?php

namespace App\Filament\Imports;

use App\Models\Department;
use App\Models\Employee;
use App\Models\SubDivision;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class EmployeeImporter extends Importer
{
    protected static ?string $model = Employee::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('employee_no')
                ->label('Employee No')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('name')
                ->label('Employee Name')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('email')
                ->label('Email'),

            // CARA TERBAIK: Gunakan relationship dengan resolveUsing
            ImportColumn::make('department')
                ->relationship(resolveUsing: function (string $state): ?Department {
                    // Dokumentasi: resolveUsing harus mengembalikan record yang ditemukan/dibuat
                    return Department::firstOrCreate([
                        'name' => trim($state),
                    ]);
                })
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('subDivision')
                ->relationship(resolveUsing: function (?string $state, array $data): ?SubDivision {
                    // 1. Cek apakah state kosong/null
                    $subDivisionName = $state ? trim($state) : null;

                    // JIKA KOSONG, kembalikan null agar data Employee tetap masuk tanpa SubDivision
                    if (blank($subDivisionName)) {
                        return null;
                    }

                    // 2. Ambil nama department dari data kolom lain (diperlukan untuk relasi)
                    $deptName = $data['department'] ?? null;

                    // Jika department tidak ada di CSV, kita tidak bisa buat SubDivision baru secara akurat
                    if (! $deptName) {
                        return null;
                    }

                    // Ambil ID Department yang sudah diproses atau ada di database
                    $department = Department::where('name', trim($deptName))->first();

                    // Jika department tidak ditemukan di DB, kita tidak bisa buat SubDivision
                    if (! $department) {
                        return null;
                    }

                    // 3. Jika semua data ada, baru lakukan firstOrCreate
                    return SubDivision::firstOrCreate([
                        'name' => $subDivisionName,
                        'department_id' => $department->id,
                    ]);
                })
                // Hapus atau ubah requiredMapping() jika kolom ini opsional
                ->rules(['nullable']),

            ImportColumn::make('position_name')
                ->label('Position Name'),

            ImportColumn::make('employment_status')
                ->label('Employment Status'),

            ImportColumn::make('job_grade_category')
                ->label('Job Grade Category'),

            ImportColumn::make('is_active')
                ->label('Is Active')
                ->boolean() // Dokumentasi: built-in casting untuk boolean
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Employee
    {
        // Dokumentasi: resolveRecord bertanggung jawab mengembalikan instance model.
        // Karena relationship sudah ditangani di getColumns, kita cukup menggunakan firstOrNew.

        return Employee::firstOrNew([
            'employee_no' => (string) $this->data['employee_no'],
        ]);
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import selesai: ' . Number::format($import->successful_rows) . ' berhasil.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' gagal.';
        }

        return $body;
    }
}
