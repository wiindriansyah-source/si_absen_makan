<?php

namespace App\Filament\Imports;

use App\Models\Company;
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

            // Tambahkan kolom Company
            ImportColumn::make('company')
                ->label('Company')
                ->relationship(resolveUsing: function (string $state): ?Company {
                    return Company::firstOrCreate([
                        'name' => trim($state),
                    ]);
                })
                ->requiredMapping(),

            ImportColumn::make('department')
                ->label('Department')
                ->relationship(resolveUsing: function (string $state): ?Department {
                    return Department::firstOrCreate([
                        'name' => trim($state),
                    ]);
                })
                ->requiredMapping()
                ->rules(['required']),

            // Kolom SubDivision DIHAPUS dari sini

            ImportColumn::make('position_name')
                ->label('Position Name'),

            ImportColumn::make('employment_status')
                ->label('Employment Status'),

            ImportColumn::make('job_grade_category')
                ->label('Job Grade Category'),

            ImportColumn::make('is_active')
                ->label('Is Active')
                ->boolean()
                ->rules(['nullable', 'boolean']),
        ];
    }

    public function resolveRecord(): ?Employee
    {
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
