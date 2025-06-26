<?php

namespace App\Filament\Resources\PembukaanRekeningBaruReportResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPembukaanRekeningBaruReports extends ListRecords
{
    protected static string $resource = PembukaanRekeningBaruReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}