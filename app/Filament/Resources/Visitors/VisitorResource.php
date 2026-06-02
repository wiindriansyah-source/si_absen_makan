<?php

namespace App\Filament\Resources\Visitors;

use App\Filament\Resources\Visitors\Pages\ManageVisitors;
use App\Models\Visitor;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class VisitorResource extends Resource
{
    protected static ?string $model = Visitor::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-identification';
    protected static ?string $navigationLabel = 'Daftar Pengunjung';

    // Judul besar di halaman index
    protected static ?string $pluralLabel = 'Data Pengunjung & Magang';

    // Mengelompokkan menu agar sidebar terorganisir (misal: di grup Manajemen Personel)
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Personel';

    // Menampilkan jumlah pengunjung di badge sidebar secara real-time
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Memberikan warna pada badge agar menarik perhatian
    protected static ?string $navigationBadgeColor = 'info';

    // Menentukan urutan menu di dalam grupnya
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Grid::make(3)
                    ->schema([
                        // Bagian Identitas Pengunjung (Lebar 2 kolom)
                        Group::make([
                            Section::make('Profil Pengunjung')
                                ->description('Lengkapi data identitas tamu atau mahasiswa magang.')
                                ->icon('heroicon-m-user')
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Nama Lengkap')
                                        ->placeholder('Masukkan nama sesuai KTP/KTM')
                                        ->required()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-m-user'),

                                    Select::make('type')
                                        ->label('Kategori Pengunjung')
                                        ->options([
                                            'Tamu' => 'Tamu Dinas/Vendor',
                                            'Magang' => 'Mahasiswa/Siswa Magang',
                                            'Kontraktor' => 'Kontraktor Luar',
                                        ])
                                        ->required()
                                        ->native(false)
                                        ->prefixIcon('heroicon-m-identification'),
                                ]),
                        ])->columnSpan(2),

                        // Bagian Institusi (Lebar 1 kolom)
                        Group::make([
                            Section::make('Asal Instansi')
                                ->description('Informasi organisasi asal.')
                                ->icon('heroicon-m-academic-cap')
                                ->schema([
                                    TextInput::make('institution')
                                        ->label('Nama Institusi/Perusahaan')
                                        ->placeholder('Contoh: Universitas Mulawarman')
                                        ->required()
                                        ->prefixIcon('heroicon-m-building-library'),
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
                    ->label('Nama Pengunjung')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-user-circle')
                    ->iconColor('primary'),

                // Tipe Pengunjung menggunakan Badge berwarna
                TextColumn::make('type')
                    ->label('Kategori')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Magang' => 'success',
                        'Tamu' => 'info',
                        'Kontraktor' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),

                // Institusi dengan Icon Bangunan
                TextColumn::make('institution')
                    ->label('Asal Institusi')
                    ->searchable()
                    ->icon('heroicon-m-building-library')
                    ->color('gray'),

                // Waktu dibuat dengan format yang lebih informatif
                TextColumn::make('created_at')
                    ->label('Terdaftar Pada')
                    ->dateTime('d M Y')
                    ->description(fn($record) => $record->created_at->format('H:i') . ' WITA')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label('Filter Kategori')
                    ->options([
                        'Magang' => 'Magang',
                        'Tamu' => 'Tamu',
                        'Kontraktor' => 'Kontraktor',
                    ]),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ])
                    ->icon('heroicon-m-ellipsis-vertical')
                    ->tooltip('Opsi')
                    ->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-user-group')
            ->emptyStateHeading('Belum ada pengunjung')
            ->emptyStateDescription('Data tamu atau mahasiswa magang akan muncul di sini.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageVisitors::route('/'),
        ];
    }
}
