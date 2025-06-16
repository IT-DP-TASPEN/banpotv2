<?php

namespace App\Filament\Resources\OtenMasterResource\Pages;

use App\Filament\Resources\OtenMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOtenMaster extends EditRecord
{
    protected static string $resource = OtenMasterResource::class;

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
