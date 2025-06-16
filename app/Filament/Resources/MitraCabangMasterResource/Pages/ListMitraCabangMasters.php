<?php

namespace App\Filament\Resources\MitraCabangMasterResource\Pages;

use App\Filament\Resources\MitraCabangMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMitraCabangMasters extends ListRecords
{
    protected static string $resource = MitraCabangMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
