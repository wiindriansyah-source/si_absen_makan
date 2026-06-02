<?php

namespace App\Filament\Resources\Departments;

use App\Filament\Resources\Departments\Pages\ManageDepartments;
use App\Models\Department;
use BackedEnum;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
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

class DepartmentResource extends Resource
{
    protected static ?string $model = Department::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-group';

    protected static ?string $navigationLabel = 'Daftar Departemen';
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Kantor';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    // Memberikan warna pada badge
    protected static ?string $navigationBadgeColor = 'info';

    // Menentukan urutan di sidebar
    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Informasi Departemen')
                    ->description('Pastikan nama Departemen yang dimasukkan sudah sesuai dengan departemen di perusahaan.')
                    ->aside() // Opsional: memindahkan judul ke samping untuk tampilan wide
                    ->icon('heroicon-m-building-office') // Menambahkan icon di header
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Departement')
                            ->placeholder('Contoh: Informasi Teknologi')
                            ->required()
                            ->maxLength(255)
                            ->prefixIcon('heroicon-m-building-office') // Icon di dalam input
                            ->columnSpanFull(), // Agar nama memanjang penuh dalam grid
                    ])
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Detail Departemen')
                    ->icon('heroicon-m-building-office') // Menambah konteks visual
                    ->description('Informasi lengkap mengenai identitas Departemen.')
                    ->schema([
                        Group::make([
                            TextEntry::make('name')
                                ->label('Nama Departemen')
                                ->weight('bold') // Membuat nama lebih menonjol
                                ->size('lg')
                                ->icon('heroicon-m-building-office'),
                        ]),
                        Group::make([
                            Grid::make(2)
                                ->schema([
                                    TextEntry::make('created_at')
                                        ->label('Waktu Kedatangan')
                                        ->dateTime('d M Y, H:i')
                                        ->icon('heroicon-m-clock')
                                        ->placeholder('-')
                                        ->color('gray'),

                                    TextEntry::make('updated_at')
                                        ->label('Pembaruan Terakhir')
                                        ->dateTime('d M Y, H:i')
                                        ->icon('heroicon-m-arrow-path')
                                        ->placeholder('-')
                                        ->color('gray'),
                                ]),
                        ])->grow(false), // Menjaga agar metadata tidak terlalu lebar
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
                    ->label('Nama Departemen')
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Bold)
                    ->icon('heroicon-m-rectangle-group')
                    ->iconColor('primary')
                    // Menambahkan deskripsi di bawah nama jika ada (contoh: ID atau info lain)
                    ->description(fn($record) => "ID: DEPT-" . str_pad($record->id, 3, '0', STR_PAD_LEFT)),

                // Menampilkan kapan data dibuat dengan format yang cantik
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->description(fn($record) => $record->created_at->format('H:i') . ' WITA')
                    ->sortable()
                    ->color('gray')
                    ->toggleable(),

                // Badge untuk status atau info tambahan (opsional)
                // Di sini kita tampilkan jumlah relasi jika ada (misal: jumlah karyawan di dept tsb)
                TextColumn::make('employees_count')
                    ->label('Total Personel')
                    ->counts('employees') // Pastikan relasi 'employees' ada di model Department
                    ->badge()
                    ->color('success')
                    ->icon('heroicon-m-users'),
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
                    ->tooltip('Opsi')
                    ->color('gray'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateIcon('heroicon-o-building-office-2')
            ->emptyStateHeading('Belum ada departemen')
            ->emptyStateDescription('Mulai dengan menambahkan departemen baru melalui tombol di atas.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageDepartments::route('/'),
        ];
    }
}
