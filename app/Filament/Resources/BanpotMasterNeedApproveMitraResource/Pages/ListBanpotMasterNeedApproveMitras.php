<?php

namespace App\Filament\Resources\BanpotMasterNeedApproveMitraResource\Pages;

use App\Filament\Resources\BanpotMasterNeedApproveMitraResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanpotMasterNeedApproveMitras extends ListRecords
{
    protected static string $resource = BanpotMasterNeedApproveMitraResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
