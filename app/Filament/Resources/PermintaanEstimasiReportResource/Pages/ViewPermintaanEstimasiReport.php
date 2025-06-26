<?php

namespace App\Filament\Resources\PermintaanEstimasiReportResource\Pages;

use App\Filament\Resources\PermintaanEstimasiReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanEstimasiReport extends ViewRecord
{
    protected static string $resource = PermintaanEstimasiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
