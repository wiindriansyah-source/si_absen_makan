<?php

namespace App\Filament\Resources\Employees;

use App\Filament\Resources\Employees\Pages\CreateEmployee;
use App\Filament\Resources\Employees\Pages\EditEmployee;
use App\Filament\Resources\Employees\Pages\ListEmployees;
use App\Filament\Resources\Employees\Pages\ViewEmployee;
use App\Filament\Resources\Employees\Schemas\EmployeeForm;
use App\Filament\Resources\Employees\Schemas\EmployeeInfolist;
use App\Filament\Resources\Employees\Tables\EmployeesTable;
use App\Models\Employee;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;
use UnitEnum;

class EmployeeResource extends Resource
{
    protected static ?string $model = Employee::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';
    protected static ?string $navigationLabel = 'Data Karyawan';

    protected static ?string $pluralLabel = 'Master Karyawan';

    // Mengelompokkan ke dalam Manajemen Personel agar rapi di sidebar
    protected static string|UnitEnum|null $navigationGroup = 'Manajemen Personel';

    // Badge untuk menunjukkan jumlah karyawan AKTIF saja
    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    // Warna sukses (hijau) untuk menunjukkan populasi aktif
    protected static ?string $navigationBadgeColor = 'success';

    // Urutan pertama dalam grupnya
    protected static ?int $navigationSort = 1;

    // Mengizinkan pencarian global melalui bar pencarian di atas dashboard
    protected static ?string $recordTitleAttribute = 'name';

    // Menambahkan kolom pencarian global (NIK juga bisa dicari dari atas)
    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'employee_no', 'email'];
    }

    // Detail tambahan saat hasil pencarian global muncul
    public static function getGlobalSearchResultDetails(\Illuminate\Database\Eloquent\Model $record): array
    {
        return [
            'NIK' => $record->employee_no,
            'Unit' => $record->department->name,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return EmployeeForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return EmployeeInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return EmployeesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEmployees::route('/'),
            'create' => CreateEmployee::route('/create'),
            'view' => ViewEmployee::route('/{record}'),
            'edit' => EditEmployee::route('/{record}/edit'),
        ];
    }
}
