<?php

namespace App\Filament\Resources\PermintaanEstimasiApprovalResource\Pages;

use App\Filament\Resources\PermintaanEstimasiApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanEstimasiApproval extends ViewRecord
{
    protected static string $resource = PermintaanEstimasiApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
