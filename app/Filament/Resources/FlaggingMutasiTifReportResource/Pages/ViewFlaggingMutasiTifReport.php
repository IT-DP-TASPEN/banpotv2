<?php

namespace App\Filament\Resources\FlaggingMutasiTifReportResource\Pages;

use App\Filament\Resources\FlaggingMutasiTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewFlaggingMutasiTifReport extends ViewRecord
{
    protected static string $resource = FlaggingMutasiTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
