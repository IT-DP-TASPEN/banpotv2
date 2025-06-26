<?php

namespace App\Filament\Resources\OpenFlaggingTifReportResource\Pages;

use App\Filament\Resources\OpenFlaggingTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOpenFlaggingTifReport extends ViewRecord
{
    protected static string $resource = OpenFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
