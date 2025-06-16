<?php

namespace App\Filament\Resources\ParameterFeeBanpotResource\Pages;

use App\Filament\Resources\ParameterFeeBanpotResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParameterFeeBanpots extends ListRecords
{
    protected static string $resource = ParameterFeeBanpotResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
