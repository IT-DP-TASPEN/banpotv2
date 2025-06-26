<?php

namespace App\Filament\Resources\OpenFlaggingTifApprovalMitraResource\Pages;

use App\Filament\Resources\OpenFlaggingTifApprovalMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpenFlaggingTifApprovalMitra extends EditRecord
{
    protected static string $resource = OpenFlaggingTifApprovalMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}