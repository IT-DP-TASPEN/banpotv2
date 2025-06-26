<?php

namespace App\Filament\Resources\PermintaanEstimasiReportResource\Pages;

use App\Filament\Resources\PermintaanEstimasiReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanEstimasiReports extends ListRecords
{
    protected static string $resource = PermintaanEstimasiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}