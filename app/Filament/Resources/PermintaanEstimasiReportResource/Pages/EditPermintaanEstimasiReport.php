<?php

namespace App\Filament\Resources\PermintaanEstimasiReportResource\Pages;

use App\Filament\Resources\PermintaanEstimasiReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanEstimasiReport extends EditRecord
{
    protected static string $resource = PermintaanEstimasiReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}