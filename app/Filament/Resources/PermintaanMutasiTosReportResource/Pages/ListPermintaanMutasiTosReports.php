<?php

namespace App\Filament\Resources\PermintaanMutasiTosReportResource\Pages;

use App\Filament\Resources\PermintaanMutasiTosReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanMutasiTosReports extends ListRecords
{
    protected static string $resource = PermintaanMutasiTosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}