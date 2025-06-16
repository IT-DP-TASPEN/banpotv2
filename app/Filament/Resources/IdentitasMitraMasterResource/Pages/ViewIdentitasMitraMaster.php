<?php

namespace App\Filament\Resources\IdentitasMitraMasterResource\Pages;

use App\Filament\Resources\IdentitasMitraMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewIdentitasMitraMaster extends ViewRecord
{
    protected static string $resource = IdentitasMitraMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
