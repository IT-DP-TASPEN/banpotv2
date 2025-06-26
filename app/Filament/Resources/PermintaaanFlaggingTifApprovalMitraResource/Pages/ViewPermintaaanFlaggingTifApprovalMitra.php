<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifApprovalMitraResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaaanFlaggingTifApprovalMitra extends ViewRecord
{
    protected static string $resource = PermintaaanFlaggingTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
