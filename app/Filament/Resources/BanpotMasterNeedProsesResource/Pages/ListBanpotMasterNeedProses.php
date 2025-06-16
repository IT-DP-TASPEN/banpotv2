<?php

namespace App\Filament\Resources\BanpotMasterNeedProsesResource\Pages;

use App\Filament\Resources\BanpotMasterNeedProsesResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanpotMasterNeedProses extends ListRecords
{
    protected static string $resource = BanpotMasterNeedProsesResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
