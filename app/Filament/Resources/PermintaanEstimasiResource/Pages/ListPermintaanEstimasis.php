<?php

namespace App\Filament\Resources\PermintaanEstimasiResource\Pages;

use App\Filament\Resources\PermintaanEstimasiResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPermintaanEstimasis extends ListRecords
{
    protected static string $resource = PermintaanEstimasiResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
