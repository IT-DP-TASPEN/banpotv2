<?php

namespace App\Filament\Resources\FlaggingMutasiTifResource\Pages;

use App\Filament\Resources\FlaggingMutasiTifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListFlaggingMutasiTifs extends ListRecords
{
    protected static string $resource = FlaggingMutasiTifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
