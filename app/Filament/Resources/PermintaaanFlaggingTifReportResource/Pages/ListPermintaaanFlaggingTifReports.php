<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifReportResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaaanFlaggingTifReports extends ListRecords
{
    protected static string $resource = PermintaaanFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}