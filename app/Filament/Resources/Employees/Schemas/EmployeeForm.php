<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Database\Eloquent\Builder;

class EmployeeForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Employee Details')
                    ->tabs([
                        // TAB 1: INFORMASI PRIBADI
                        Tab::make('Profil Utama')
                            ->icon('heroicon-m-user')
                            ->schema([
                                Grid::make(2)->schema([
                                    TextInput::make('employee_no')
                                        ->label('Nomor Induk Karyawan (NIK)')
                                        ->placeholder('Contoh: 2024001')
                                        ->required()
                                        ->unique(ignoreRecord: true)
                                        ->prefixIcon('heroicon-m-finger-print'),

                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->placeholder('Masukkan nama sesuai KTP')
                                        ->required()
                                        ->prefixIcon('heroicon-m-identification'),

                                    TextInput::make('email')
                                        ->label('Alamat Email')
                                        ->email()
                                        ->placeholder('karyawan@indominco.com')
                                        ->prefixIcon('heroicon-m-envelope')
                                        ->columnSpanFull(),
                                ]),
                            ]),

                        // TAB 2: PENEMPATAN & JABATAN
                        Tab::make('Penempatan')
                            ->icon('heroicon-m-briefcase')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('department_id')
                                        ->label('Departemen')
                                        ->relationship('department', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->native(false)
                                        ->live() // Menjadikan input ini interaktif
                                        ->prefixIcon('heroicon-m-building-office'),

                                    Select::make('sub_division_id')
                                        ->label('Sub-Divisi')
                                        ->relationship(
                                            name: 'subDivision',
                                            titleAttribute: 'name',
                                            // Fitur Canggih: Hanya munculkan sub-divisi milik departemen terpilih
                                            modifyQueryUsing: fn(Get $get, Builder $query) =>
                                            $query->where('department_id', $get('department_id'))
                                        )
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->native(false)
                                        ->prefixIcon('heroicon-m-swatch'),

                                    TextInput::make('position_name')
                                        ->label('Jabatan')
                                        ->placeholder('Contoh: Senior Supervisor')
                                        ->prefixIcon('heroicon-m-user-group'),

                                    Select::make('job_grade_category')
                                        ->label('Golongan / Grade')
                                        ->options([
                                            'Staff' => 'Staff',
                                            'Supervisor' => 'Supervisor',
                                            'Manager' => 'Manager',
                                            'Executive' => 'Executive',
                                        ])
                                        ->native(false)
                                        ->prefixIcon('heroicon-m-academic-cap'),
                                ]),
                            ]),

                        // TAB 3: STATUS PEGAWAI
                        Tab::make('Status Kerja')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Grid::make(2)->schema([
                                    Select::make('employment_status')
                                        ->label('Status Kontrak')
                                        ->options([
                                            'Permanent' => 'Karyawan Tetap',
                                            'Contract' => 'Kontrak (PKWT)',
                                            'Probation' => 'Masa Percobaan',
                                        ])
                                        ->native(false)
                                        ->prefixIcon('heroicon-m-clipboard-document-check'),

                                    Group::make([
                                        Toggle::make('is_active')
                                            ->label('Status Aktif')
                                            ->helperText('Nonaktifkan jika karyawan sudah resign/pensiun')
                                            ->onColor('success')
                                            ->offColor('danger')
                                            ->default(true),
                                    ])->extraAttributes(['class' => 'pt-6']),
                                ]),
                            ]),
                    ])->columnSpanFull()
            ]);
    }
}
