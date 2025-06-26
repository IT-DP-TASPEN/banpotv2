<?php

namespace App\Filament\Resources\OpenFlaggingTifReportResource\Pages;

use App\Filament\Resources\OpenFlaggingTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListOpenFlaggingTifReports extends ListRecords
{
    protected static string $resource = OpenFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}