<?php

namespace App\Filament\Resources\PermintaanCheckingResource\Pages;

use App\Filament\Resources\PermintaanCheckingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanChecking extends ViewRecord
{
    protected static string $resource = PermintaanCheckingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
