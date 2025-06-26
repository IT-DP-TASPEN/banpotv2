<?php

namespace App\Filament\Resources\PermintaaanFlaggingTifReportResource\Pages;

use App\Filament\Resources\PermintaaanFlaggingTifReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPermintaaanFlaggingTifReport extends EditRecord
{
    protected static string $resource = PermintaaanFlaggingTifReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}