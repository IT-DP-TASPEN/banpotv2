<?php

namespace App\Filament\Resources\BanpotMasterReportResource\Pages;

use App\Filament\Resources\BanpotMasterReportResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanpotMasterReport extends ViewRecord
{
    protected static string $resource = BanpotMasterReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
