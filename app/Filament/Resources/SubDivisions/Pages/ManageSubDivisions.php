<?php

namespace App\Filament\Resources\SubDivisions\Pages;

use App\Filament\Resources\SubDivisions\SubDivisionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSubDivisions extends ManageRecords
{
    protected static string $resource = SubDivisionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
