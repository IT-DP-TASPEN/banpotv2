<?php

namespace App\Filament\Resources\ParameterBiayaResource\Pages;

use App\Filament\Resources\ParameterBiayaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListParameterBiayas extends ListRecords
{
    protected static string $resource = ParameterBiayaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
