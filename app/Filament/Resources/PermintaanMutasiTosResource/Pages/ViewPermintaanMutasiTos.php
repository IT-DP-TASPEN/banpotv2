<?php

namespace App\Filament\Resources\PermintaanMutasiTosResource\Pages;

use App\Filament\Resources\PermintaanMutasiTosResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanMutasiTos extends ViewRecord
{
    protected static string $resource = PermintaanMutasiTosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
