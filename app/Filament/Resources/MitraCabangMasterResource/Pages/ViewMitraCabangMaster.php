<?php

namespace App\Filament\Resources\MitraCabangMasterResource\Pages;

use App\Filament\Resources\MitraCabangMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMitraCabangMaster extends ViewRecord
{
    protected static string $resource = MitraCabangMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
