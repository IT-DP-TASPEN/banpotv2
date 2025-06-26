<?php

namespace App\Filament\Resources\OpenFlaggingTifReportResource\Pages;

use App\Filament\Resources\OpenFlaggingTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditOpenFlaggingTifReport extends EditRecord
{
    protected static string $resource = OpenFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}