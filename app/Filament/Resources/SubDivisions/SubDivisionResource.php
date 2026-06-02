<?php

namespace App\Filament\Resources\SubDivisions;

use App\Filament\Resources\SubDivisions\Pages\ManageSubDivisions;
use App\Models\SubDivision;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
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

class SubDivisionResource extends Resource
{
    protected static ?string $model = SubDivision::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';
    protected static ?string $navigationLabel = 'Sub-Divisi';

    // Judul jamak untuk header halaman
    protected static ?string $pluralLabel = 'Daftar Sub-Divisi';

    // Mengelompokkan di bawah grup yang sama dengan Departemen
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Kantor';

    // Menampilkan jumlah total sub-divisi di badge sidebar
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Memberikan warna badge yang berbeda (misal: warna indigo/violet)
    protected static ?string $navigationBadgeColor = 'gray';

    // Mengatur urutan agar muncul tepat setelah Departemen
    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Struktur Sub-Divisi')
                    ->description('Tentukan nama sub-divisi dan hubungkan dengan departemen induknya.')
                    ->icon('heroicon-m-swatch') // Ikon yang melambangkan bagian/sub-unit
                    ->aside() // Membuat judul & deskripsi di samping (sangat cocok untuk form pendek)
                    ->schema([
                        Grid::make(1) // Layout satu kolom di dalam section agar fokus
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Sub-Divisi')
                                    ->placeholder('Contoh: IT Infrastructure, Accounting, dll.')
                                    ->required()
                                    ->maxLength(255)
                                    ->prefixIcon('heroicon-m-tag'),

                                Select::make('department_id')
                                    ->label('Departemen Induk')
                                    ->relationship('department', 'name')
                                    ->placeholder('Pilih departemen...')
                                    ->searchable()
                                    ->preload() // Memuat data awal agar pencarian instan
                                    ->required()
                                    ->prefixIcon('heroicon-m-building-office')
                                    ->native(false), // Tampilan dropdown modern ala Filament
                            ]),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3) // Menggunakan grid 3 kolom untuk keseimbangan visual
                    ->schema([
                        // Bagian Kiri: Informasi Inti (Lebar 2 kolom)
                        Group::make([
                            Section::make('Detail Sub-Divisi')
                                ->icon('heroicon-m-swatch')
                                ->schema([
                                    TextEntry::make('name')
                                        ->label('Nama Sub-Divisi')
                                        ->color('primary')
                                        ->icon('heroicon-m-tag'),

                                    TextEntry::make('department.name')
                                        ->label('Departemen Induk')
                                        ->icon('heroicon-m-building-office')
                                        ->color('gray'),
                                ]),
                        ])->columnSpan(2),

                        // Bagian Kanan: Informasi Tambahan/Waktu (Lebar 1 kolom)
                        Group::make([
                            Section::make('Riwayat Data')
                                ->icon('heroicon-m-clock')
                                ->compact() // Padding lebih tipis agar terlihat ringkas
                                ->schema([
                                    TextEntry::make('created_at')
                                        ->label('Dibuat')
                                        ->dateTime('d M Y, H:i'),

                                    TextEntry::make('updated_at')
                                        ->label('Terakhir Diperbarui')
                                        ->dateTime('d M Y, H:i')
                                        ->color('gray'),
                                ]),
                        ])->columnSpan(1),
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name')
                    ->label('Sub-Divisi')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-swatch')
                    ->iconColor('primary')
                    ->description(fn($record) => "Internal Unit dari " . ($record->department->name ?? 'N/A')),

                // Kolom Departemen sebagai Badge agar mudah dibedakan
                TextColumn::make('department.name')
                    ->label('Departemen Induk')
                    ->badge()
                    ->color('gray')
                    ->searchable()
                    ->sortable(),

                // Menampilkan jumlah anggota/personel jika relasi 'employees' tersedia
                TextColumn::make('employees_count')
                    ->label('Personel')
                    ->counts('employees') // Pastikan relasi ini ada di model SubDivision
                    ->badge()
                    ->color('info')
                    ->alignCenter(),

                // Waktu dibuat dengan format yang lebih bersih
                TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y')
                    ->description(fn($record) => $record->created_at->format('H:i'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->color('gray'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-swatch')
            ->emptyStateHeading('Belum ada Sub-Divisi')
            ->emptyStateDescription('Tambahkan sub-divisi baru untuk mulai mengorganisir departemen Anda.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageSubDivisions::route('/'),
        ];
    }
}
