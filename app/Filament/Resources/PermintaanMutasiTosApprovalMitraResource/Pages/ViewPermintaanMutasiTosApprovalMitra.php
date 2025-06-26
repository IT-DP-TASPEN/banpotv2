<?php

namespace App\Filament\Resources\PermintaanMutasiTosApprovalMitraResource\Pages;

use App\Filament\Resources\PermintaanMutasiTosApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanMutasiTosApprovalMitra extends ViewRecord
{
    protected static string $resource = PermintaanMutasiTosApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
