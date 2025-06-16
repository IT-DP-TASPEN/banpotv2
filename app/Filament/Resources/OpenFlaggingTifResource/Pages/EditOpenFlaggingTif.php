<?php

namespace App\Filament\Resources\OpenFlaggingTifResource\Pages;

use App\Filament\Resources\OpenFlaggingTifResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpenFlaggingTif extends EditRecord
{
    protected static string $resource = OpenFlaggingTifResource::class;

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
