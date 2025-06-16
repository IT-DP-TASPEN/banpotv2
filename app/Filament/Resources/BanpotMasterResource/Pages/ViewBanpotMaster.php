<?php

namespace App\Filament\Resources\BanpotMasterResource\Pages;

use App\Filament\Resources\BanpotMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBanpotMaster extends ViewRecord
{
    protected static string $resource = BanpotMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
