<?php

namespace App\Filament\Widgets;

use App\Models\MealAttendance;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class MealSatisfactionChart extends ChartWidget
{
    protected ?string $heading = 'Persentase Kepuasan (Bulan Ini)';
    protected static ?int $sort = 3;

    // Memberikan efek donut agar terlihat lebih modern daripada pie standar
    protected function getData(): array
    {
        // Mengambil data hanya untuk bulan yang sedang berjalan
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $puas = MealAttendance::where('satisfaction', 'Puas')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        $tidakPuas = MealAttendance::where('satisfaction', 'Tidak Puas')
            ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->count();

        // Jika data kosong, kita berikan nilai 0 agar chart tidak error
        return [
            'datasets' => [
                [
                    'label' => 'Tingkat Kepuasan',
                    'data' => [$puas, $tidakPuas],
                    'backgroundColor' => [
                        '#22c55e', // Hijau (Success)
                        '#ef4444', // Merah (Danger)
                    ],
                    'hoverOffset' => 4,
                ],
            ],
            'labels' => ['Puas', 'Tidak Puas'],
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
}
