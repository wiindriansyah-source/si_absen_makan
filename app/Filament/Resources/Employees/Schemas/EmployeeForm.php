<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
// use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
// use Illuminate\Database\Eloquent\Builder;

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

                                    Select::make('company_id')
                                        ->label('Perusahaan')
                                        ->placeholder('Pilih Perusahaan')
                                        ->relationship('company', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->columnSpanFull()
                                        ->required(),
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
                                        ->prefixIcon('heroicon-m-building-office'),
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
