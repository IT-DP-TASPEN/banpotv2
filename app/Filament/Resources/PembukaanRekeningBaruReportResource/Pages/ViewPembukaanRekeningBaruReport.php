<?php

namespace App\Filament\Resources\PembukaanRekeningBaruReportResource\Pages;

use App\Filament\Resources\PembukaanRekeningBaruReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPembukaanRekeningBaruReport extends ViewRecord
{
    protected static string $resource = PembukaanRekeningBaruReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
