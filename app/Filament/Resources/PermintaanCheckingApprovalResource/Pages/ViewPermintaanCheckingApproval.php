<?php

namespace App\Filament\Resources\PermintaanCheckingApprovalResource\Pages;

use App\Filament\Resources\PermintaanCheckingApprovalResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewPermintaanCheckingApproval extends ViewRecord
{
    protected static string $resource = PermintaanCheckingApprovalResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
