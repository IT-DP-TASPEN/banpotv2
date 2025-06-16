<?php

namespace App\Filament\Resources\BanpotMasterNeedApproveMitraResource\Pages;

use App\Filament\Resources\BanpotMasterNeedApproveMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanpotMasterNeedApproveMitra extends EditRecord
{
    protected static string $resource = BanpotMasterNeedApproveMitraResource::class;

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
