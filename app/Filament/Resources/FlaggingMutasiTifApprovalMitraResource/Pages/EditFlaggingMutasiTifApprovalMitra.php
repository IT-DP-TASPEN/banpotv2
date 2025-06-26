<?php

namespace App\Filament\Resources\FlaggingMutasiTifApprovalMitraResource\Pages;

use App\Filament\Resources\FlaggingMutasiTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlaggingMutasiTifApprovalMitra extends EditRecord
{
    protected static string $resource = FlaggingMutasiTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}