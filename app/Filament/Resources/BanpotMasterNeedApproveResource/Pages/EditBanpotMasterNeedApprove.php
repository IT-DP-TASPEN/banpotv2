<?php

namespace App\Filament\Resources\BanpotMasterNeedApproveResource\Pages;

use App\Filament\Resources\BanpotMasterNeedApproveResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanpotMasterNeedApprove extends EditRecord
{
    protected static string $resource = BanpotMasterNeedApproveResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }
}
