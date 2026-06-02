<?php

namespace App\Filament\Resources\Employees\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

class EmployeeInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Informasi Karyawan')
                        ->icon('heroicon-m-user-circle')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('name')
                                    ->label('Nama Lengkap')
                                    ->weight(FontWeight::Bold)
                                    ->color('primary')
                                    ->columnSpanFull(),

                                TextEntry::make('employee_no')
                                    ->label('NIK (Nomor Induk Karyawan)')
                                    ->copyable() // Memudahkan admin copy NIK
                                    ->icon('heroicon-m-finger-print'),

                                TextEntry::make('email')
                                    ->label('Alamat Email')
                                    ->icon('heroicon-m-envelope')
                                    ->color('info')
                                    ->url(fn($record) => "mailto:{$record->email}"),
                            ]),
                        ]),

                    Section::make('Struktur & Jabatan')
                        ->icon('heroicon-m-briefcase')
                        ->schema([
                            Grid::make(2)->schema([
                                TextEntry::make('department.name')
                                    ->label('Departemen')
                                    ->weight(FontWeight::Medium),

                                TextEntry::make('subDivision.name')
                                    ->default('Tidak Ada Sub Divisi')
                                    ->label('Sub-Divisi'),

                                TextEntry::make('position_name')
                                    ->label('Jabatan / Posisi')
                                    ->badge()
                                    ->color('gray'),

                                TextEntry::make('job_grade_category')
                                    ->label('Golongan / Grade')
                                    ->icon('heroicon-m-academic-cap'),
                            ]),
                        ]),
                ])->grow(),

                // Kolom Kanan: Status & Audit Trail
                Group::make([
                    Section::make('Status Kepegawaian')
                        ->schema([
                            IconEntry::make('is_active')
                                ->label('Status Akun')
                                ->boolean()
                                ->alignCenter(),

                            TextEntry::make('employment_status')
                                ->label('Status Kerja')
                                ->badge()
                                ->color(fn(string $state): string => match ($state) {
                                    'Permanent' => 'success',
                                    'Contract' => 'warning',
                                    default => 'gray',
                                })
                                ->alignCenter(),
                        ]),

                    Section::make('Jejak Audit')
                        ->compact()
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Terdaftar')
                                ->dateTime('d M Y'),

                            TextEntry::make('updated_at')
                                ->label('Update Terakhir')
                                ->dateTime('d M Y H:i')
                                ->color('gray'),
                        ]),
                ])->columnSpan(1),
            ]);
    }
}
