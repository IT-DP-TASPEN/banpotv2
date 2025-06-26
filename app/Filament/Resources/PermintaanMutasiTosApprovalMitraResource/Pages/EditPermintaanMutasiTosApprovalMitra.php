<?php

namespace App\Filament\Resources\PermintaanMutasiTosApprovalMitraResource\Pages;

use App\Filament\Resources\PermintaanMutasiTosApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanMutasiTosApprovalMitra extends EditRecord
{
    protected static string $resource = PermintaanMutasiTosApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}