<?php

namespace App\Filament\Resources\IdentitasMitraMasterResource\Pages;

use App\Filament\Imports\IdentitasMitraMasterImporter;
use App\Filament\Resources\IdentitasMitraMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListIdentitasMitraMasters extends ListRecords
{
    protected static string $resource = IdentitasMitraMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\ImportAction::make()
                ->importer(IdentitasMitraMasterImporter::class),
        ];
    }
}