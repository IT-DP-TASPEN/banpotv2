<?php

namespace App\Filament\Resources\BanpotMasterNeedProsesResource\Pages;

use App\Filament\Resources\BanpotMasterNeedProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanpotMasterNeedProses extends EditRecord
{
    protected static string $resource = BanpotMasterNeedProsesResource::class;

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
