<?php

namespace App\Filament\Resources\BanpotMasterNeedProsesResource\Pages;

use App\Filament\Resources\BanpotMasterNeedProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanpotMasterNeedProses extends ViewRecord
{
    protected static string $resource = BanpotMasterNeedProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
