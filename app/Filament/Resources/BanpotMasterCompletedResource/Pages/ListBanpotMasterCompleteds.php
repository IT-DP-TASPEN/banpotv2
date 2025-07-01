<?php

namespace App\Filament\Resources\BanpotMasterCompletedResource\Pages;

use App\Filament\Resources\BanpotMasterCompletedResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBanpotMasterCompleteds extends ListRecords
{
    protected static string $resource = BanpotMasterCompletedResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
