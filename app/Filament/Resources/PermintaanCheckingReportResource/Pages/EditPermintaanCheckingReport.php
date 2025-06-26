<?php

namespace App\Filament\Resources\PermintaanCheckingReportResource\Pages;

use App\Filament\Resources\PermintaanCheckingReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaanCheckingReport extends EditRecord
{
    protected static string $resource = PermintaanCheckingReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}