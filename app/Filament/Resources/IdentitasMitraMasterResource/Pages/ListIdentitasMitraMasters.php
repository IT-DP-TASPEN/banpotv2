<?php

namespace App\Filament\Resources\IdentitasMitraMasterResource\Pages;

use App\Filament\Imports\IdentitasMitraMasterImporter;
use App\Filament\Resources\IdentitasMitraMasterResource;
use App\Imports\IdentitasMitraMasterImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;

class ListIdentitasMitraMasters extends ListRecords
{
    protected static string $resource = IdentitasMitraMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            // Actions\ImportAction::make()
            //     ->importer(IdentitasMitraMasterImporter::class),
            ExcelImportAction::make()
                ->color('primary')
                ->label('Import Identitas Master')
                ->use(IdentitasMitraMasterImport::class),
        ];
    }
}
