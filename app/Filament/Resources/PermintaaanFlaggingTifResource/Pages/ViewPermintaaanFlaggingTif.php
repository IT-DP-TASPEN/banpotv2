<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaaanFlaggingTif extends ViewRecord
{
    protected static string $resource = PermintaaanFlaggingTifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
