<?php

namespace App\Filament\Resources\BanpotMasterNeedProsesMitraResource\Pages;

use App\Filament\Resources\BanpotMasterNeedProsesMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanpotMasterNeedProsesMitra extends ViewRecord
{
    protected static string $resource = BanpotMasterNeedProsesMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
