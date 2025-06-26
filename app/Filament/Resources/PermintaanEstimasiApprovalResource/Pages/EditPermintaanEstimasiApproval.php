<?php

namespace App\Filament\Resources\PermintaanEstimasiApprovalResource\Pages;

use App\Filament\Resources\PermintaanEstimasiApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanEstimasiApproval extends EditRecord
{
    protected static string $resource = PermintaanEstimasiApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}