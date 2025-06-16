<?php

namespace App\Filament\Resources\FlaggingMutasiTifResource\Pages;

use App\Filament\Resources\FlaggingMutasiTifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlaggingMutasiTif extends EditRecord
{
    protected static string $resource = FlaggingMutasiTifResource::class;

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
