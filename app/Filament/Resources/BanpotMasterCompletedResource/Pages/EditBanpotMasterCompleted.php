<?php

namespace App\Filament\Resources\BanpotMasterCompletedResource\Pages;

use App\Filament\Resources\BanpotMasterCompletedResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanpotMasterCompleted extends EditRecord
{
    protected static string $resource = BanpotMasterCompletedResource::class;

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
