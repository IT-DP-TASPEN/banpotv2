<?php

namespace App\Filament\Resources\FlaggingMutasiTifReportResource\Pages;

use App\Filament\Resources\FlaggingMutasiTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditFlaggingMutasiTifReport extends EditRecord
{
    protected static string $resource = FlaggingMutasiTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}