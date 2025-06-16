<?php

namespace App\Filament\Resources\OtenMasterResource\Pages;

use App\Filament\Resources\OtenMasterResource;
use App\Imports\OtenMasterImport;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use EightyNine\ExcelImport\ExcelImportAction;

class ListOtenMasters extends ListRecords
{
    protected static string $resource = OtenMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->color("primary")
                ->label('Import Oten Master')
                ->use(OtenMasterImport::class),
            Actions\CreateAction::make(),
        ];
    }
}