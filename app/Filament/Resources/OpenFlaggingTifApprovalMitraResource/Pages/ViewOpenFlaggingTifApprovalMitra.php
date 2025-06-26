<?php

namespace App\Filament\Resources\OpenFlaggingTifApprovalMitraResource\Pages;

use App\Filament\Resources\OpenFlaggingTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOpenFlaggingTifApprovalMitra extends ViewRecord
{
    protected static string $resource = OpenFlaggingTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
