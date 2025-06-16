<?php

namespace App\Filament\Resources\OtenMasterResource\Pages;

use App\Filament\Resources\OtenMasterResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewOtenMaster extends ViewRecord
{
    protected static string $resource = OtenMasterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
