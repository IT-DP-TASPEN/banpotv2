<?php

namespace App\Filament\Resources\BanpotMasterReportResource\Pages;

use App\Filament\Resources\BanpotMasterReportResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditBanpotMasterReport extends EditRecord
{
    protected static string $resource = BanpotMasterReportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
        ];
    }
}