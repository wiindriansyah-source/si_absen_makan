<?php

namespace App\Filament\Pages;

use App\Models\Employee;
use App\Models\MealAttendance;
use App\Models\Visitor;
use BackedEnum;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Pages\Dashboard as BaseDashboard;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Carbon;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class Dashboard extends BaseDashboard
{
    protected static ?string $title = 'Dasboard';
    protected static string $routePath = '/';
    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedHome;
    protected static string|BackedEnum|null $activeNavigationIcon = Heroicon::Home;
    protected static ?int $navigationSort = -2;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export Laporan PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('danger')
                ->schema([
                    Select::make('report_type') // <-- 3. Gunakan Select::make, bukan SelectAction
                        ->label('Pilih Jenis Laporan')
                        ->options([
                            'all' => 'Semua Laporan',
                            'stats' => 'Ringkasan Statistik (Stats Overview)',
                            'attendance' => 'Tren Kehadiran Makan Harian',
                            'satisfaction' => 'Detail Tingkat Kepuasan',
                        ])
                        ->default('all') // <-- 4. Beri nilai default agar tidak pernah null
                        ->required(),

                    DateRangePicker::make('date_range')
                        ->label('Rentang Tanggal')
                        ->placeholder('Pilih Rentang Tanggal')
                        ->default(now()->subDays(6)->format('d/m/Y') . ' - ' . now()->format('d/m/Y'))
                        ->displayFormat('DD/MM/YYYY')
                        ->maxDate(now())
                        ->required(),
                ])
                ->action(function (array $data) {
                    $reportType = $data['report_type'];
                    $parts = explode(' - ', $data['date_range']);
                    $startDate = Carbon::createFromFormat('d/m/Y', trim($parts[0]))->startOfDay();
                    $endDate = Carbon::createFromFormat('d/m/Y', trim($parts[1]))->endOfDay();

                    // --- 1. AMBIL DATA RINGKASAN (EXISTING) ---
                    $karyawanAktif = Employee::where('is_active', true)->count();
                    $totalPengunjung = Visitor::whereBetween('created_at', [$startDate, $endDate])->count();
                    $totalAbsensi = MealAttendance::whereBetween('created_at', [$startDate, $endDate])->count();

                    $puas = MealAttendance::where('satisfaction', 'Puas')
                        ->whereBetween('created_at', [$startDate, $endDate])->count();
                    $tidakPuas = MealAttendance::where('satisfaction', 'Tidak Puas')
                        ->whereBetween('created_at', [$startDate, $endDate])->count();

                    $trenHarian = MealAttendance::selectRaw('DATE(created_at) as date, count(*) as total')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->groupBy('date')
                        ->orderBy('date', 'asc')
                        ->get();

                    // --- 2. AMBIL DATA DETAIL ---
                    // Detail Karyawan Aktif
                    $employeesList = Employee::with(['department', 'company'])
                        ->where('is_active', true)
                        ->orderBy('name', 'asc')
                        ->get();

                    // Detail Pengunjung
                    $visitorsList = Visitor::whereBetween('created_at', [$startDate, $endDate])
                        ->orderBy('created_at', 'desc')
                        ->get();

                    // Detail Absensi & Kepuasan
                    $attendanceDetails = MealAttendance::with(['employee', 'visitor', 'department'])
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->orderBy('created_at', 'desc')
                        ->get();

                    // --- 3. KIRIM KE VIEW ---
                    $pdfData = [
                        'reportType' => $reportType,
                        'startDate' => $startDate->translatedFormat('d F Y'),
                        'endDate' => $endDate->translatedFormat('d F Y'),
                        'karyawanAktif' => $karyawanAktif,
                        'totalPengunjung' => $totalPengunjung,
                        'totalAbsensi' => $totalAbsensi,
                        'puas' => $puas,
                        'tidakPuas' => $tidakPuas,
                        'trenHarian' => $trenHarian,
                        // Data baru:
                        'employeesList' => $employeesList,
                        'visitorsList' => $visitorsList,
                        'attendanceDetails' => $attendanceDetails,
                    ];

                    $pdf = Pdf::loadView('pdf.dashboard-report', $pdfData);

                    // Agar proses generate PDF dengan banyak data tidak timeout
                    set_time_limit(300);

                    $typeName = $reportType === 'all' ? 'Lengkap' : ucfirst($reportType);
                    $filename = "Laporan-{$typeName}-" . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.pdf';

                    return response()->streamDownload(function () use ($pdf) {
                        echo $pdf->output();
                    }, $filename);
                }),
        ];
    }
}
