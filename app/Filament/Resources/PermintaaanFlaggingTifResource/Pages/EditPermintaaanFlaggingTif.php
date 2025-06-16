<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaaanFlaggingTif extends EditRecord
{
    protected static string $resource = PermintaaanFlaggingTifResource::class;

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
