<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifApprovalMitraResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaaanFlaggingTifApprovalMitra extends EditRecord
{
    protected static string $resource = PermintaaanFlaggingTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}