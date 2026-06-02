<?php

namespace App\Filament\Resources\Employees\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class EmployeesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Karyawan')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => "NIK: " . $record->employee_no)
                    ->copyable()
                    ->copyMessage('Nama disalin'),

                TextColumn::make('company.name')
                    ->label('Perusahaan')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                // Kolom Departemen (Sub-Divisi dihilangkan)
                TextColumn::make('department.name')
                    ->label('Unit Kerja')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('employment_status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Permanent' => 'success',
                        'Contract' => 'warning',
                        'Probation' => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-badge')
                    ->falseIcon('heroicon-o-x-circle')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Tgl Bergabung')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('department')
                    ->relationship('department', 'name')
                    ->label('Filter Departemen')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('employment_status')
                    ->label('Status Kerja')
                    ->options([
                        'Permanent' => 'Tetap',
                        'Contract' => 'Kontrak',
                        'Probation' => 'Probation',
                    ]),

                TernaryFilter::make('is_active')
                    ->label('Status Akun')
                    ->placeholder('Semua')
                    ->trueLabel('Aktif Saja')
                    ->falseLabel('Non-Aktif Saja'),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('Opsi Karyawan'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
