<?php

namespace App\Filament\Resources\MealAttendances;

use App\Filament\Resources\MealAttendances\Pages\ManageMealAttendances;
use App\Models\MealAttendance;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class MealAttendanceResource extends Resource
{
    protected static ?string $model = MealAttendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationLabel = 'Presensi Makan';

    // Memberikan judul halaman yang lebih profesional
    protected static ?string $pluralLabel = 'Data Absensi Makan';

    // Mengelompokkan menu agar sidebar rapi
    protected static string|UnitEnum|null  $navigationGroup = 'Laporan Harian';

    // Menampilkan jumlah absensi hari ini sebagai Badge di sidebar
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::whereDate('created_at', now())->count();
    }

    // Memberikan warna sukses (hijau) pada badge jika ada data masuk
    protected static ?string $navigationBadgeColor = 'success';

    // Urutan posisi menu di sidebar
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'id'; // Biasanya ID lebih unik untuk record makan

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        // Kolom Kiri: Data Utama (Identitas)
                        Group::make([
                            Section::make('Identitas Kehadiran')
                                ->icon('heroicon-m-user-group')
                                ->schema([
                                    Select::make('employee_id')
                                        ->label('Karyawan/Personel')
                                        ->relationship('employee', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->prefixIcon('heroicon-m-user'),

                                    Select::make('department_id')
                                        ->label('Departemen')
                                        ->relationship('department', 'name')
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->prefixIcon('heroicon-m-building-office'),
                                ]),
                        ])->columnSpan(2),

                        // Kolom Kanan: Detail Makan
                        Group::make([
                            Section::make('Informasi Makan')
                                ->icon('heroicon-m-clock')
                                ->schema([
                                    Select::make('meal_type')
                                        ->label('Tipe')
                                        ->options([
                                            'Kantin' => 'Kantin',
                                            'Kotakan' => 'Kotakan',
                                        ])
                                        ->required()
                                        ->native(false),

                                    Select::make('meal_time')
                                        ->label('Waktu')
                                        ->options([
                                            'Pagi' => 'Pagi',
                                            'Siang' => 'Siang',
                                            'Malam' => 'Malam',
                                        ])
                                        ->required()
                                        ->native(false),
                                ]),
                        ])->columnSpan(1),

                        // Baris Bawah: Kepuasan & Feedback (Full Width)
                        Section::make('Feedback Pelayanan')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->schema([
                                Grid::make(2)
                                    ->schema([
                                        Radio::make('satisfaction')
                                            ->label('Tingkat Kepuasan')
                                            ->options([
                                                'Puas' => 'Puas',
                                                'Tidak Puas' => 'Tidak Puas',
                                            ])
                                            ->descriptions([
                                                'Puas' => 'Rasa makanan dan pelayanan sudah baik.',
                                                'Tidak Puas' => 'Perlu perbaikan pada rasa atau porsi.',
                                            ])
                                            ->required()
                                            ->inline(),

                                        Textarea::make('feedback')
                                            ->label('Kritik & Saran')
                                            ->placeholder('Tuliskan detail feedback jika ada...')
                                            ->rows(3),
                                    ]),
                            ])->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make([
                    Section::make('Data Personel')
                        ->icon('heroicon-m-user-circle')
                        ->schema([
                            TextEntry::make('employee.name')
                                ->label('Nama Karyawan/Personel')
                                ->weight('bold')
                                ->size('lg')
                                ->color('primary')
                                ->icon('heroicon-m-user'),

                            TextEntry::make('department.name')
                                ->label('Departemen Asal')
                                ->icon('heroicon-m-building-office-2'),
                        ]),

                    Section::make('Ulasan & Feedback')
                        ->icon('heroicon-m-chat-bubble-left-right')
                        ->schema([
                            TextEntry::make('satisfaction')
                                ->label('Tingkat Kepuasan')
                                ->badge() // Mengubah teks menjadi badge
                                ->color(fn(string $state): string => match ($state) {
                                    'Puas' => 'success',
                                    'Tidak Puas' => 'danger',
                                    default => 'gray',
                                })
                                ->icon(fn(string $state): string => match ($state) {
                                    'Puas' => 'heroicon-m-face-smile',
                                    'Tidak Puas' => 'heroicon-m-face-frown',
                                    default => 'heroicon-m-question-mark-circle',
                                }),

                            TextEntry::make('feedback')
                                ->label('Catatan/Saran')
                                ->prose() // Membuat teks lebih nyaman dibaca jika panjang
                                ->markdown()
                                ->placeholder('Tidak ada feedback tambahan.')
                                ->columnSpanFull(),
                        ]),
                ])->grow(),

                // Kolom Kanan: Logistik & Waktu (Sisi Samping)
                Group::make([
                    Section::make('Detail Logistik')
                        ->schema([
                            TextEntry::make('meal_type')
                                ->label('Jenis Hidangan')
                                ->icon('heroicon-m-shopping-bag')
                                ->weight('medium'),

                            TextEntry::make('meal_time')
                                ->label('Sesi Makan')
                                ->icon('heroicon-m-sun')
                                ->color('warning'),
                        ]),

                    Section::make('Jejak Audit')
                        ->compact()
                        ->schema([
                            TextEntry::make('created_at')
                                ->label('Waktu Submit')
                                ->dateTime('d M Y, H:i')
                                ->size('sm')
                                ->color('gray'),

                            TextEntry::make('updated_at')
                                ->label('Perubahan Terakhir')
                                ->dateTime('d M Y, H:i')
                                ->size('sm')
                                ->color('gray'),
                        ]),
                ])->columnSpan(1),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('display_name')
                    ->label('Personel')
                    ->getStateUsing(fn($record) => $record->employee->name ?? $record->visitor->name ?? '-')
                    ->searchable(['name']) // Pastikan index pencarian mengarah ke kolom yang benar
                    ->weight(FontWeight::Bold)
                    ->description(fn($record) => $record->employee_id ? 'Karyawan Perusahaan' : 'Magang / Tamu Luar'),

                // Departemen dengan Badge Soft
                TextColumn::make('department.name')
                    ->label('Departemen')
                    ->default('Visitor')
                    ->badge()
                    ->color('gray')
                    ->searchable(),

                // Tipe Makan dengan Icon
                TextColumn::make('meal_type')
                    ->label('Tipe')
                    ->icon(fn($state): string => $state === 'Kantin' ? 'heroicon-m-building-storefront' : 'heroicon-m-gift-top')
                    ->color('info'),

                // Waktu Makan dengan Warna Berbeda
                TextColumn::make('meal_time')
                    ->label('Waktu')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Pagi' => 'warning',
                        'Siang' => 'success',
                        'Malam' => 'indigo',
                        default => 'gray',
                    }),

                // Kepuasan dengan Badge & Icon
                TextColumn::make('satisfaction')
                    ->label('Kepuasan')
                    ->badge()
                    ->icon(fn(string $state): string => $state === 'Puas' ? 'heroicon-m-face-smile' : 'heroicon-m-face-frown')
                    ->color(fn(string $state): string => match ($state) {
                        'Puas' => 'success',
                        'Tidak Puas' => 'danger',
                        default => 'gray',
                    }),

                // Tanggal dikelompokkan agar rapi
                TextColumn::make('created_at')
                    ->label('Waktu Input')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->size('sm')
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->color('gray')
                    ->tooltip('Opsi'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc'); // Menampilkan data terbaru di paling atas
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageMealAttendances::route('/'),
        ];
    }
}
