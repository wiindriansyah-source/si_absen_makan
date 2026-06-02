<?php

namespace App\Filament\Widgets;

use App\Models\Employee;
use App\Models\MealAttendance;
use App\Models\Visitor;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;
    protected function getStats(): array
    {
        $puas = MealAttendance::where('satisfaction', 'Puas')->count();
        $tidakPuas = MealAttendance::where('satisfaction', 'Tidak Puas')->count();
        // Hitung rata-rata kepuasan (asumsi skala 1-5)
        $avgSatisfaction = MealAttendance::avg('satisfaction') ?? 0;

        return [
            // Stat 1: Karyawan Aktif
            Stat::make('Karyawan Aktif', Employee::where('is_active', true)->count())
                ->description('Total personil saat ini')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('success'),

            // Stat 2: Total Pengunjung
            Stat::make('Total Pengunjung', Visitor::count())
                ->description('Termasuk tamu & magang')
                ->descriptionIcon('heroicon-m-identification')
                ->color('info'),

            // Stat 3: Absensi Makan Hari Ini
            Stat::make('Absensi Makan', MealAttendance::whereDate('created_at', Carbon::today())->count())
                ->description('Jumlah makan hari ini')
                ->descriptionIcon('heroicon-m-clipboard-document-check')
                ->color('warning'),

            // Stat 4: Skor Kepuasan
            Stat::make('Tingkat Kepuasan', $puas . ' Puas')
                ->description($tidakPuas . ' Tidak Puas')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($puas >= $tidakPuas ? 'success' : 'danger'),
        ];
    }
}
