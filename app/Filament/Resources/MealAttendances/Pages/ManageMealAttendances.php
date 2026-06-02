<?php

namespace App\Filament\Resources\MealAttendances\Pages;

use App\Filament\Resources\MealAttendances\MealAttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageMealAttendances extends ManageRecords
{
    protected static string $resource = MealAttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
