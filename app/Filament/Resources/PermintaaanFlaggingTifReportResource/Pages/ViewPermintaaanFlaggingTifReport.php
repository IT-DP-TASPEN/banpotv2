<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifReportResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaaanFlaggingTifReport extends ViewRecord
{
    protected static string $resource = PermintaaanFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
