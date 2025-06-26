<?php

namespace App\Filament\Resources\BanpotMasterReportResource\Pages;

use App\Filament\Resources\BanpotMasterReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanpotMasterReports extends ListRecords
{
    protected static string $resource = BanpotMasterReportResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}