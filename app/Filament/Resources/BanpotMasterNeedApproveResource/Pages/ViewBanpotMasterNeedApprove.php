<?php

namespace App\Filament\Resources\BanpotMasterNeedApproveResource\Pages;

use App\Filament\Resources\BanpotMasterNeedApproveResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanpotMasterNeedApprove extends ViewRecord
{
    protected static string $resource = BanpotMasterNeedApproveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
