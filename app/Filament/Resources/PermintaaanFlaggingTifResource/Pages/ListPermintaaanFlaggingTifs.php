<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaaanFlaggingTifs extends ListRecords
{
    protected static string $resource = PermintaaanFlaggingTifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
