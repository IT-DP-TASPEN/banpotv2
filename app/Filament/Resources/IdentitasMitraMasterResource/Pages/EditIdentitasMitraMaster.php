<?php

namespace App\Filament\Resources\IdentitasMitraMasterResource\Pages;

use App\Filament\Resources\IdentitasMitraMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIdentitasMitraMaster extends EditRecord
{
    protected static string $resource = IdentitasMitraMasterResource::class;

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
