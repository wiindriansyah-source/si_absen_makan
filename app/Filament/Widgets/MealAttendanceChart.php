<?php

namespace App\Filament\Widgets;

use App\Models\MealAttendance;
use Filament\Schemas\Components\Form;
use Filament\Schemas\Schema;
use Filament\Widgets\ChartWidget;
use Filament\Widgets\ChartWidget\Concerns\HasFiltersSchema;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Malzariey\FilamentDaterangepickerFilter\Fields\DateRangePicker;

class MealAttendanceChart extends ChartWidget
{
    use HasFiltersSchema;

    protected ?string $heading = 'Tren Kehadiran Makan';
    protected static ?int $sort = 2;

    /**
     * 1. Deklarasikan Filter Menggunakan Schema
     */
    public function filtersSchema(Schema $schema): Schema
    {
        return $schema->components([
            DateRangePicker::make('created_at_range')
                ->label('Rentang Tanggal')
                ->placeholder('Pilih Rentang Tanggal')
                // PERBAIKAN: Format default disamakan menggunakan d/m/Y (garis miring)
                ->default(now()->subDays(6)->format('d/m/Y') . ' - ' . now()->format('d/m/Y'))
                ->displayFormat('DD/MM/YYYY')
                ->maxDate(now()),
        ]);
    }

    /**
     * 2. Ambil Data Berdasarkan Rentang yang Dipilih
     */
    protected function getData(): array
    {
        $rangeString = $this->filters['created_at_range'] ?? null;

        // Fallback jika komponen filter belum termuat sempurna
        if (empty($rangeString)) {
            $startDate = now()->subDays(6)->startOfDay();
            $endDate = now()->endOfDay();
        } else {
            // Pecah string range menjadi tanggal Start dan End
            $parts = explode(' - ', $rangeString);

            // Gunakan createFromFormat karena input pasti menggunakan '/' (d/m/Y)
            $startDate = isset($parts[0])
                ? Carbon::createFromFormat('d/m/Y', trim($parts[0]))->startOfDay()
                : now()->subDays(6)->startOfDay();

            $endDate = isset($parts[1])
                ? Carbon::createFromFormat('d/m/Y', trim($parts[1]))->endOfDay()
                : now()->endOfDay();
        }

        // Hitung selisih hari
        $diffInDays = $startDate->diffInDays($endDate);

        // Inisialisasi Query Utama
        $query = MealAttendance::select([
            DB::raw('count(*) as total')
        ])->whereBetween('created_at', [$startDate, $endDate]);

        // Tentukan logika pengelompokan (Auto-Grouping)
        if ($diffInDays > 60) {
            $query->addSelect(DB::raw("DATE_FORMAT(created_at, '%Y-%m') as date_group"))
                ->groupBy('date_group')
                ->orderBy('date_group', 'asc');

            $dateFormat = 'F Y';
        } else {
            $query->addSelect(DB::raw('DATE(created_at) as date_group'))
                ->groupBy('date_group')
                ->orderBy('date_group', 'asc');

            $dateFormat = 'd M';
        }

        $data = $query->get();
        $dbData = $data->pluck('total', 'date_group')->toArray();

        $cleanLabels = [];
        $cleanTotals = [];

        // Isi slot kosong agar grafik presisi ke angka 0 jika tidak ada absensi
        if ($diffInDays > 60) {
            $current = $startDate->copy()->startOfMonth();
            while ($current->lessThanOrEqualTo($endDate)) {
                $key = $current->format('Y-m');
                $cleanLabels[] = $current->translatedFormat($dateFormat);
                $cleanTotals[] = $dbData[$key] ?? 0;
                $current->addMonth();
            }
        } else {
            $current = $startDate->copy();
            while ($current->lessThanOrEqualTo($endDate)) {
                $key = $current->format('Y-m-d');
                $cleanLabels[] = $current->translatedFormat($dateFormat);
                $cleanTotals[] = $dbData[$key] ?? 0;
                $current->addDay();
            }
        }

        return [
            'datasets' => [
                [
                    'label' => 'Total Porsi Terlayani',
                    'data' => $cleanTotals,
                    'backgroundColor' => '#36A2EB',
                    'borderColor' => '#36A2EB',
                    'fill' => 'start',
                    'tension' => 0.3,
                ],
            ],
            'labels' => $cleanLabels,
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
