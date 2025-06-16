<?php

namespace App\Filament\Resources\OpenFlaggingTifResource\Pages;

use App\Filament\Resources\OpenFlaggingTifResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOpenFlaggingTif extends ViewRecord
{
    protected static string $resource = OpenFlaggingTifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
