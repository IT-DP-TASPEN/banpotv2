<?php

namespace App\Filament\Resources\PermintaanEstimasiResource\Pages;

use App\Filament\Resources\PermintaanEstimasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanEstimasi extends ViewRecord
{
    protected static string $resource = PermintaanEstimasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
