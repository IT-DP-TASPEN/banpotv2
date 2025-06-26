<?php

namespace App\Filament\Resources\BanpotMasterNeedApproveResource\Pages;

use App\Filament\Resources\BanpotMasterNeedApproveResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanpotMasterNeedApproves extends ListRecords
{
    protected static string $resource = BanpotMasterNeedApproveResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
