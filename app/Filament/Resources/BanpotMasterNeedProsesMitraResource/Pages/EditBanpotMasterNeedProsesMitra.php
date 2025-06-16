<?php

namespace App\Filament\Resources\BanpotMasterNeedProsesMitraResource\Pages;

use App\Filament\Resources\BanpotMasterNeedProsesMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanpotMasterNeedProsesMitra extends EditRecord
{
    protected static string $resource = BanpotMasterNeedProsesMitraResource::class;

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
