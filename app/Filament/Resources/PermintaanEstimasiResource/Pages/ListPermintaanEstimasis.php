<?php

namespace App\Filament\Resources\PermintaanEstimasiResource\Pages;

use Filament\Actions;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PermintaanEstimasiExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\PermintaanEstimasiResource;

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
