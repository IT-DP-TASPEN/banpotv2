<?php

namespace App\Filament\Resources\BanpotMasterNeedProsesMitraResource\Pages;

use App\Filament\Resources\BanpotMasterNeedProsesMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanpotMasterNeedProsesMitras extends ListRecords
{
    protected static string $resource = BanpotMasterNeedProsesMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
