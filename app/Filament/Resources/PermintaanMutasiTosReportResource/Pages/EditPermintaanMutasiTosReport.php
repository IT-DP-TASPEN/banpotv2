<?php

namespace App\Filament\Resources\PermintaanMutasiTosReportResource\Pages;

use App\Filament\Resources\PermintaanMutasiTosReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanMutasiTosReport extends EditRecord
{
    protected static string $resource = PermintaanMutasiTosReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}